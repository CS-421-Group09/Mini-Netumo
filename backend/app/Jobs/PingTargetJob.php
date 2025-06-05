<?php

namespace App\Jobs;

use App\Models\Target;
use App\Models\Check;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class PingTargetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $target;

    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    public function handle()
    {
        $start = microtime(true);
        $statusCode = null;
        $latency = null;
        $isSuccess = false;
        $errorMessage = null;

        try {
            $ch = curl_init($this->target->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            if ($result === false) {
                $errorMessage = curl_error($ch);
                Log::error("Ping failed for {$this->target->url}: $errorMessage");
            } else {
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $latency = (int)((microtime(true) - $start) * 1000);
                $isSuccess = $statusCode >= 200 && $statusCode < 400;
                Log::info("Ping result for {$this->target->url}: status $statusCode, latency {$latency}ms, success: " . ($isSuccess ? 'yes' : 'no'));
            }
            curl_close($ch);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            Log::critical("Ping exception for {$this->target->url}: $errorMessage");
        }

        Check::create([
            'target_id' => $this->target->id,
            'status_code' => $statusCode,
            'latency_ms' => $latency,
            'is_success' => $isSuccess,
            'error_message' => $errorMessage,
        ]);

        // After logging the check, check for two consecutive failures
        $recentChecks = \App\Models\Check::where('target_id', $this->target->id)
            ->latest()
            ->take(2)
            ->pluck('is_success');

        if ($recentChecks->count() == 2 && $recentChecks->every(fn($v) => !$v)) {
            \App\Models\Alert::create([
                'target_id' => $this->target->id,
                'type' => 'downtime',
                'message' => 'Two consecutive downtime checks failed.',
                'meta' => null,
                'notified_at' => null,
            ]);
        }
    }
} 