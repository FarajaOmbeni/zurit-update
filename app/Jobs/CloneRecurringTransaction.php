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
            $clone = $this->prototype->replicate([
                'created_at',
                'updated_at',
                'next_run_at'
            ]);
            $clone->transaction_date = today();
            $clone->save();

            // 2. Duplicate the specialised record (income / expense)
            //    Detect which relationship exists:
            if ($this->prototype->income) {
                $clone->income()->create(
                    $this->prototype->income->only(['amount', 'income_date', 'category_id'])
                );
            } elseif ($this->prototype->expense) {
                $clone->expense()->create(
                    $this->prototype->expense->only(['amount', 'expense_date', 'category_id'])
                );
            }
            // 3. Bump the prototype’s next_run_at to next month
            $this->prototype->update([
                'next_run_at' => Carbon::parse($this->prototype->next_run_at)->addMonth()
            ]);
        });
    }
}