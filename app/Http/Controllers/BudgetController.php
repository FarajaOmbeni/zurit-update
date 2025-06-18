<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Goal;
use Inertia\Inertia;
use App\Models\Income;
use App\Models\Expense;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\RecurrenceRule;
use App\Models\GoalContribution;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;


class BudgetController extends Controller
{
    // app/Http/Controllers/BudgetController.php
    public function upsertRule(Transaction $txn, Request $r): ?RecurrenceRule
    {
        /* 1. If user unticks the box */
        if (!$r->boolean('is_recurring')) {

            // deactivate the existing rule (if any)
            if ($txn->rule) {
                $txn->rule->update(['is_active' => false]);
                return $txn->rule;          // ← keep the object so caller can leave rule_id
            }

            // create nothing, leave rule_id = null for brand‑new non‑recurring txn
            return null;
        }

        /* 2. User wants this to be recurring */
        $data = [
            'user_id'     => $txn->user_id,
            'type'        => $txn->type,
            'investment_id'=> $r->input('investment_id'),
            'category'    => $r->category === 'Other' ? $r->otherCategory : $r->category,
            'amount'      => $r->amount,
            'description' => $r->description,
            'pattern'     => $r->recurrence_pattern ?? 'monthly',
            'next_run_on' => now()->addMonth()->startOfMonth(),
            'is_active'   => true,
        ];

        return $txn->rule
            ? tap($txn->rule)->update($data)     // edit existing
            : RecurrenceRule::create($data);     // or make new
    }


    public function index()
    {
        $user = auth()->user();
        $currentMonthString = now()->format('F');

        // Eager load relationships with current month constraints
        $user->load([
            'incomes',
            'transactions',
            'expenses' ,
            'goals',
            'debts',
            'investments'
        ]);

        $data = [
            'incomes'     => $user->incomes()->whereMonth('income_date', now()->month)->get(),
            'expenses'    => $user->expenses()->whereMonth('expense_date', now()->month)->get(),
            'transactions' => $user->transactions()->whereMonth('transaction_date', now()->month)->get(),
            'goals'       => $user->goals,
            'debts'       => $user->debts,
            'investments' => $user->investments,
        ];

        return Inertia::render('UserDashboard/BudgetPlanner', [
            'data' => $data,
            'today' => $currentMonthString,
        ]);
    }

    public function budgets()
    {
        $user = auth()->user();
        $currentMonthString = now()->format('F');

        // Eager load relationships with current month constraints
        $user->load([
            'incomes',
            'transactions',
            'expenses',
        ]);

        $data = [
            'incomes'     => $user->incomes,
            'expenses'    => $user->expenses,
            'transactions' => $user->transactions,
        ];

        return Inertia::render('UserDashboard/Budgets', [
            'data' => $data,
            'today' => $currentMonthString,
        ]);
    }

    public function storeIncome(Request $request)
    {        
        $request->validate([
            'amount'      => 'required|numeric',
            'category'    => 'required|string',
            'description' => 'required|string',
            'income_date' => 'required|date',
            'is_recurring' => 'required|boolean'
        ]);

        DB::transaction(function () use ($request) {
            $txn = new Transaction([
                'user_id'          => auth()->id(),
                'type'             => 'income',
                'category'         => $request->category === 'Other' ? $request->otherCategory : $request->category,
                'amount'           => $request->amount,
                'transaction_date' => $request->income_date,
                'description'      => $request->description,
            ]);

            $rule = $this->upsertRule($txn, $request);
            /* only (re)attach when the user said “yes” AND there’s a rule row */
            if ($request->boolean('is_recurring') && $rule) {
                $txn->rule_id = $rule->id;
            }
            $txn->save();


            $txn->income()->create([
                'user_id'     => $txn->user_id,
                'category'    => $txn->category,
                'amount'      => $txn->amount,
                'description' => $txn->description,
                'income_date' => $request->income_date,
            ]);
        });

        return to_route('budget.index');
    }


    public function storeExpense(Request $request)
    {
        $request->validate([
            'category'     => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'description'  => 'required|string|max:255',
            'expense_date' => 'required|date|max:255',
            'is_recurring' => 'required|boolean'
        ]);

        DB::transaction(function () use ($request) {
            $txn = new Transaction([
                'user_id'          => auth()->id(),
                'type'             => 'expense',
                'category'         => $request->category === 'Other' ? $request->otherCategory : $request->category,
                'amount'           => $request->amount,
                'transaction_date' => $request->expense_date,
                'description'      => $request->description,
            ]);

            $rule = $this->upsertRule($txn, $request);
            /* only (re)attach when the user said “yes” AND there’s a rule row */
            if ($request->boolean('is_recurring') && $rule) {
                $txn->rule_id = $rule->id;
            }
            $txn->save();


            $txn->expense()->create([
                'user_id'     => $txn->user_id,
                'category'    => $txn->category,
                'amount'      => $txn->amount,
                'description' => $txn->description,
                'expense_date' => $request->expense_date,
            ]);
        });


        return to_route('budget.index');
    }

    public function destroyIncome($id)
    {
        $transaction = Transaction::find($id);

        if ($transaction) {
            $transaction->delete();
        }

        return to_route('budget.index');
    }

    public function updateExpense(Request $request, $id)
    {
        $request->validate([
            'category'         => 'required|string|max:255',
            'amount'           => 'required|numeric',
            'description'      => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'is_recurring'      => 'required|boolean',
        ]);

        DB::transaction(function () use ($request, $id) {
            $txn   = Transaction::findOrFail($id);
            $child = $txn->expense;           // or $txn->expense in updateExpense

            // 1) update or create the rule
            $rule = $this->upsertRule($txn, $request);
            if ($rule) $txn->rule_id = $rule->id;

            // 2) overwrite txn & child with new data
            $newCategory = $request->category === 'Other' ? $request->otherCategory : $request->category;

            $txn->update([
                'category'         => $newCategory,
                'amount'           => $request->amount,
                'transaction_date' => $request->transaction_date,
                'description'      => $request->description,
            ]);

            $child->update([
                'category'  => $newCategory,
                'amount'    => $request->amount,
                'expense_date' => $request->transaction_date,   // expense_date in updateExpense
                'description' => $request->description,
            ]);
        });


        return to_route('budget.index');
    }

    public function updateIncome(Request $request, $id)
    {
        $request->validate([
            'category'         => 'required|string|max:255',
            'amount'           => 'required|numeric',
            'description'      => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'is_recurring'      => 'required|boolean',
        ]);

        DB::transaction(function () use ($request, $id) {
            $txn   = Transaction::findOrFail($id);
            $child = $txn->income;           // or $txn->expense in updateExpense

            // 1) update or create the rule
            $rule = $this->upsertRule($txn, $request);
            if ($rule) $txn->rule_id = $rule->id;

            // 2) overwrite txn & child with new data
            $newCategory = $request->category === 'Other' ? $request->otherCategory : $request->category;

            $txn->update([
                'category'         => $newCategory,
                'amount'           => $request->amount,
                'transaction_date' => $request->transaction_date,
                'description'      => $request->description,
            ]);

            $child->update([
                'category'  => $newCategory,
                'amount'    => $request->amount,
                'income_date' => $request->transaction_date,   // expense_date in updateExpense
                'description' => $request->description,
            ]);
        });

        return to_route('budget.index');
    }

    public function destroyExpense($id)
    {
        DB::transaction(function () use ($id) {
            $transaction = Transaction::find($id);
            if ($transaction) {
                // If this is a Debt Payment, update the related debt record.
                if ($transaction->category === 'Debt Payment') {
                    // Find the debt payment record linked to this transaction.
                    $debtPayment = DebtPayment::where('transaction_id', $transaction->id)->first();
                    if ($debtPayment) {
                        // Retrieve the associated debt.
                        $debt = Debt::find($debtPayment->debt_id);
                        if ($debt) {
                            // Reduce the debt's current_amount by the transaction's amount.
                            $debt->current_amount -= $transaction->amount;
                            $debt->update();
                        }
                    }
                } else if ($transaction->category === 'Goal Contribution') {
                    // Find the debt payment record linked to this transaction.
                    $goalContribution = GoalContribution::where('transaction_id', $transaction->id)->first();
                    if ($goalContribution) {
                        // Retrieve the associated debt.
                        $goal = Goal::find($goalContribution->goal_id);
                        if ($goal) {
                            // Reduce the debt's current_amount by the transaction's amount.
                            $goal->current_amount -= $transaction->amount;
                            $goal->update();
                        }
                    }
                }

                // Delete the transaction record.
                $transaction->delete();
            }
        });

        return to_route('budget.index');
    }
}
