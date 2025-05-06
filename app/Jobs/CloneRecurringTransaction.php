<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

// app/Jobs/CloneRecurringTransaction.php
class CloneRecurringTransaction implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public Transaction $prototype) {}

    public function handle(): void
    {
        DB::transaction(function () {
            // 1. Duplicate the transaction row (except primary key & dates)
            $clone = $this->prototype->replicate();
            $clone->transaction_date = today();
            $clone->is_recurring = false;
            $clone->next_run_at = null;
            $clone->parent_transaction_id = $this->prototype->id;
            $clone->save();

            // 2. Duplicate the specialised record (income / expense)
            //    Detect which relationship exists:
            if ($this->prototype->income) {
                $data = $this->prototype->income
                            ->replicate(['id', 'transaction_id', 'created_at', 'updated_at'])
                            ->toArray();
                $data['income_date'] = today();
                $clone->income()->create($data);

            } elseif ($this->prototype->expense) {
                $data = $this->prototype->expense
                    ->replicate(['id', 'transaction_id', 'created_at', 'updated_at'])
                    ->toArray();
                $data['expense_date'] = today();

                $clone->expense()->create($data);

            }
            // 3. Bump the prototype’s next_run_at to next month
            $this->prototype->update([
                'next_run_at' => Carbon::parse($this->prototype->next_run_at)->addMonth()->startOfMonth()
            ]);
        });
    }
}