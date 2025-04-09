<?php
namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class CreateRecurrentTransactions extends Command
{
    protected $signature = 'create:recurrent-transactions';
    protected $description = 'Create recurrent transactions based on defined patterns';

    public function handle()
    {
        $recurrentTransactions = Transaction::where('is_recurring', 'yes')->get();

        foreach ($recurrentTransactions as $transaction) {
            $this->createRecurringTransactions($transaction);
        }

        $this->info('Recurrent transactions created successfully.');
    }

    protected function createRecurringTransactions($transaction)
    {
        $recurrencePattern = $transaction->recurrence_pattern;
        $nextTransactionDate = now();

        switch ($recurrencePattern) {
            case 'daily':
                $nextTransactionDate->addDay();
                break;
            case 'monthly':
                $nextTransactionDate->addMonth();
                break;
            case 'quarterly':
                $nextTransactionDate->addQuarter();
                break;
            case 'yearly':
                $nextTransactionDate->addYear();
                break;
            default:
                $this->error('Invalid recurrence pattern for transaction ID: ' . $transaction->id);
                return;
        }

        Transaction::create([
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'category' => $transaction->category,
            'date' => $nextTransactionDate,
            'is_recurring' => 'yes',
            'recurrence_pattern' => $recurrencePattern,
        ]);
    }
}
