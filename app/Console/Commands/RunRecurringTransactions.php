<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use App\Jobs\CloneRecurringTransaction;

// app/Console/Commands/RunRecurringTransactions.php
class RunRecurringTransactions extends Command
{
    protected $signature = 'transactions:run-recurring';
    protected $description = 'Clone any recurring transactions due today';

    public function handle(): int
    {
        Transaction::query()
            ->where('is_recurring', true)
            ->whereDate('next_run_at', today()->format('Y-m-d'))
            ->chunkById(50, function ($transactions) {
                $transactions->each(fn($t) => CloneRecurringTransaction::dispatch($t));
            });

        $this->info('Recurring run completed.');
        return self::SUCCESS;
    }
}
