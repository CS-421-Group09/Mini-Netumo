<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Target;
use App\Jobs\PingTargetJob;
use App\Models\Check;
use App\Models\Alert;

class DispatchPingJobs extends Command
{
    protected $signature = 'monitor:ping';
    protected $description = 'Dispatch ping jobs for all active targets';

    public function handle()
    {
        $targets = Target::where('is_active', true)->get();
        foreach ($targets as $target) {
            dispatch(new PingTargetJob($target));
        }
        $this->info('Dispatched ping jobs for ' . $targets->count() . ' targets.');
    }
} 