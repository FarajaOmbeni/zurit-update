<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\RecurrenceRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

// app/Jobs/SpawnTransactionFromRule.php
class SpawnTransactionFromRule implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(public RecurrenceRule $rule) {}

    public function handle(): void
    {
        DB::transaction(function () {
            /*   Make the core transaction */
            $txn = $this->rule->transactions()->create([
                'user_id'          => $this->rule->user_id,
                'type'             => $this->rule->type,     // income | expense
                'category'         => $this->rule->category,
                'amount'           => $this->rule->amount,
                'transaction_date' => today(),
                'description'      => $this->rule->description,
            ]);

            /*   Spawn correct child row */
            if ($this->rule->type === 'income') {
                $txn->income()->create([
                    'user_id'     => $txn->user_id,
                    'category'    => $txn->category,
                    'amount'      => $txn->amount,
                    'description' => $txn->description,
                    'income_date' => today(),
                ]);
            } else {                        // expense
                $txn->expense()->create([
                    'user_id'      => $txn->user_id,
                    'category'     => $txn->category,
                    'amount'       => $txn->amount,
                    'description'  => $txn->description,
                    'expense_date' => today(),
                ]);
            }

            /*   Push next_run_on forward */
            $this->rule->update([
                'next_run_on' => Carbon::parse($this->rule->next_run_on)
                    ->addMonth()
                    ->startOfMonth(),
            ]);
        });
    }
}
