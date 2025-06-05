<?php

namespace App\Jobs;

use App\Models\Target;
use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckSslAndDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $target;

    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    public function handle()
    {
        $sslExpiryDays = null;
        $domainExpiryDays = null;
        $now = Carbon::now();

        // --- SSL Certificate Expiry ---
        try {
            $urlParts = parse_url($this->target->url);
            $host = $urlParts['host'] ?? null;
            if ($host) {
                $context = stream_context_create([
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ]);
                $client = @stream_socket_client("ssl://{$host}:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
                if ($client) {
                    $params = stream_context_get_params($client);
                    $cert = $params['options']['ssl']['peer_certificate'] ?? null;
                    if ($cert) {
                        $certInfo = openssl_x509_parse($cert);
                        if (isset($certInfo['validTo_time_t'])) {
                            $expiry = Carbon::createFromTimestamp($certInfo['validTo_time_t']);
                            $sslExpiryDays = $now->diffInDays($expiry, false);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('SSL check failed: ' . $e->getMessage());
        }

        // --- Domain Expiry (WHOIS) ---
        try {
            $host = $urlParts['host'] ?? null;
            if ($host) {
                $whois = shell_exec("whois {$host}");
                if ($whois) {
                    if (preg_match('/Expiry Date:\s*(.+)/i', $whois, $matches) || preg_match('/Registry Expiry Date:\s*(.+)/i', $whois, $matches)) {
                        $expiry = Carbon::parse(trim($matches[1]));
                        $domainExpiryDays = $now->diffInDays($expiry, false);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Domain WHOIS check failed: ' . $e->getMessage());
        }

        // Update target
        $this->target->ssl_checked_at = $now;
        $this->target->ssl_expiry_days = $sslExpiryDays;
        $this->target->domain_checked_at = $now;
        $this->target->domain_expiry_days = $domainExpiryDays;
        $this->target->save();

        // Raise alerts if needed
        if (!is_null($sslExpiryDays) && $sslExpiryDays <= 14) {
            Alert::create([
                'target_id' => $this->target->id,
                'type' => 'ssl',
                'message' => "SSL certificate expires in {$sslExpiryDays} days.",
                'meta' => null,
                'notified_at' => null,
            ]);
        }
        if (!is_null($domainExpiryDays) && $domainExpiryDays <= 14) {
            Alert::create([
                'target_id' => $this->target->id,
                'type' => 'domain',
                'message' => "Domain expires in {$domainExpiryDays} days.",
                'meta' => null,
                'notified_at' => null,
            ]);
        }
    }
} 