<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Debt;
use Inertia\Inertia;
use App\Models\Income;
use App\Models\Expense;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;

class BudgetController extends Controller
{
    use NetIncomeCalculator;

    public function index()
    {
        $user = auth()->user();
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $currentMonthString = now()->format('F');

        // Eager load relationships with current month constraints
        $user->load([
            'incomes' => function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('income_date', $currentMonth)
                    ->whereYear('income_date', $currentYear);
            },
            'transactions' => function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('transaction_date', $currentMonth)
                    ->whereYear('transaction_date', $currentYear);
            },
            'expenses' => function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('expense_date', $currentMonth)
                    ->whereYear('expense_date', $currentYear);
            },
            'goals',
            'goals.contributions.transaction',
            'debts',
            'debts.payments.transaction',
            'investments',
            'investments.contributions.transaction',
        ]);

        $data = [
            'incomes'     => $user->incomes,
            'expenses'    => $user->expenses,
            'transactions' => $user->transactions,
            'goals'       => $user->goals,
            'debts'       => $user->debts,
            'investments' => $user->investments,
        ];

        return Inertia::render('UserDashboard/BudgetPlanner', [
            'data' => $data,
            'currentMonth' => $currentMonthString,
        ]);
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'amount'       => 'required|numeric',
            'category'     => 'required|string',
            'description'  => 'required|string',
            'income_date'  => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            // Create the transaction first
            $transaction = new Transaction();
            $transaction->user_id = auth()->id();
            $transaction->type = 'income';
            $transaction->category = $request->category;
            $transaction->amount = $request->amount;
            $transaction->transaction_date = $request->income_date;
            $transaction->description = $request->description;
            $transaction->save();

            // Now create the income and assign the transaction id as a foreign key
            $income = new Income();
            $income->user_id = auth()->id();
            $income->category = $request->category;
            $income->amount = $request->amount;
            $income->description = $request->description;
            $income->income_date = $request->income_date;
            $income->transaction_id = $transaction->id;
            $income->save();
        });

        return to_route('budget.index');
    }


    public function storeExpense(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Create the transaction first
            $transaction = new Transaction();
            $transaction->user_id = auth()->id();
            $transaction->type = 'expense';
            $transaction->category = $request->category;
            $transaction->amount = $request->amount;
            $transaction->transaction_date = $request->expense_date;
            $transaction->description = $request->description;
            $transaction->save();

            // Now create the expense and assign the transaction id as a foreign key
            $expense = new Expense();
            $expense->user_id = auth()->id();
            $expense->category = $request->category;
            $expense->amount = $request->amount;
            $expense->description = $request->description;
            $expense->expense_date = $request->expense_date;
            $expense->transaction_id = $transaction->id;
            $expense->save();
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
            'transaction_date' => 'required|date', // used as the income_date
        ]);

        DB::transaction(function () use ($request, $id) {
            // Use the transaction ID (passed from the front end) to update the Transaction.
            $transaction = Transaction::findOrFail($id);

            // Retrieve the related Income record by matching its transaction_id
            $expense = Expense::where('transaction_id', $transaction->id)->firstOrFail();

            // Update the Transaction first
            $transaction->category         = $request->category;
            $transaction->amount           = $request->amount;
            $transaction->description      = $request->description;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->update();

            // Now update the related Expense record
            $expense->category    = $request->category;
            $expense->amount      = $request->amount;
            $expense->description = $request->description;
            $expense->expense_date = $request->transaction_date;
            $expense->update();
        });

        return to_route('budget.index');
    }

    public function updateIncome(Request $request, $id)
    {
        $request->validate([
            'category'         => 'required|string|max:255',
            'amount'           => 'required|numeric',
            'description'      => 'required|string|max:255',
            'transaction_date' => 'required|date', // used as the income_date
        ]);

        DB::transaction(function () use ($request, $id) {
            // Use the transaction ID (passed from the front end) to update the Transaction.
            $transaction = Transaction::findOrFail($id);

            // Retrieve the related Income record by matching its transaction_id
            $income = Income::where('transaction_id', $transaction->id)->firstOrFail();

            // Update the Transaction first
            $transaction->category         = $request->category;
            $transaction->amount           = $request->amount;
            $transaction->description      = $request->description;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->save();

            // Now update the related Income record
            $income->category    = $request->category;
            $income->amount      = $request->amount;
            $income->description = $request->description;
            $income->income_date = $request->transaction_date;
            $income->save();
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
                }

                // Delete the transaction record.
                $transaction->delete();
            }
        });

        return to_route('budget.index');
    }
}
