<?php

namespace App\Console\Commands;

use App\Models\RecurrenceRule;
use Illuminate\Console\Command;
use App\Jobs\SpawnTransactionFromRule;

class RunRecurrenceRules extends Command
{
    protected $signature = 'run:recurring';
    protected $description = 'Clone any recurring transactions due today';

    public function handle()
    {
        RecurrenceRule::where('is_active', true)
            ->whereDate('next_run_on', today())  
            ->chunkById(
                50,
                fn($rules) =>
                $rules->each(fn($r) => SpawnTransactionFromRule::dispatch($r))
            );
    }
}
