<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Target;
use App\Jobs\CheckSslAndDomainJob;

class DispatchSslAndDomainChecks extends Command
{
    protected $signature = 'monitor:check-expiry';
    protected $description = 'Dispatch SSL and domain expiry check jobs for all active targets';

    public function handle()
    {
        $targets = Target::where('is_active', true)->get();
        foreach ($targets as $target) {
            dispatch(new CheckSslAndDomainJob($target));
        }
        $this->info('Dispatched SSL and domain expiry check jobs for ' . $targets->count() . ' targets.');
    }
} 