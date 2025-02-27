<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Traits\NetIncomeCalculator;
use Inertia\Inertia;

class BudgetController extends Controller
{
    use NetIncomeCalculator;

    public function index()
    {
        $userId = auth()->id();
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $currentMonthString = Carbon::now()->format('F');

        // Fetch income and expenses for the current user for this month
        $income = Income::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->get();

        $expenses = Expense::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->get();

        $budget = $income->concat($expenses)
            ->sortByDesc(function ($transaction) {
                // Parse created_at to a timestamp for consistent comparison
                return Carbon::parse($transaction->created_at)->timestamp;
            })
            ->values(); // Re-index the collection

        $recentTransactions = $budget->map(function ($transaction) {
            return [
                'id'          => $transaction->id,
                'type'        => $transaction instanceof Income ? 'income' : 'expense',
                'category'    => $transaction instanceof Income ? $transaction->income_type : $transaction->expense_type,
                'description' => $transaction->description, // taken as is, may be null for incomes
                'amount'      => intval($transaction instanceof Income ? $transaction->actual_income : $transaction->actual_expense),
                'date'        => Carbon::parse($transaction->created_at)->toDateString(),
            ];
        });



        // Format income data for BudgetBarChart
        $incomeData = $income->groupBy('income_type')->map(function ($items, $incomeType) {
            $totalAmount = $items->sum(function ($item) {
                return intval($item->actual_income);
            });

            return [
                'amount' => $totalAmount,
                'label' => $incomeType,
                'currency' => 'KES',
            ];
        })->values();

        // Format expense data for BudgetBarChart
        $expenseData = $expenses->groupBy('expense_type')->map(function ($items, $expenseType) {
            $totalAmount = $items->sum(function ($item) {
                return intval($item->actual_expense);
            });
            return [
                'amount' => $totalAmount,
                'label' => $expenseType,
                'currency' => 'KES',
            ];
        })->filter(function ($item) {
            return $item['amount'] > 0;
        })->values()->all();

        // Calculate totals (optional, you can do this in Vue too)
        $actualIncome = intval($income->sum('actual_income'));
        $actualExpenses = intval($expenses->sum('actual_expense'));
        $netIncome = intval($actualIncome - $actualExpenses);
        $hasAnyData = $income->isNotEmpty() || $expenses->isNotEmpty();

        return Inertia::render('UserDashboard/BudgetPlanner', [
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'recentTransactions' => $recentTransactions,
            'actualIncome' => $actualIncome,
            'actualExpenses' => $actualExpenses,
            'netIncome' => $netIncome,
            'hasAnyData' => $hasAnyData,
            'currentMonthString' => $currentMonthString,
        ]);
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        $income = new Income();
        $income->user_id = auth()->id();
        $income->income_type = $request->type;
        $income->actual_income = $request->amount;
        $income->save();
        
        return to_route('budget.index');
    }


    public function storeExpense(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
        ]);

        $expense = new Expense();
        $expense->user_id = auth()->id();
        $expense->expense_type = $request->type;
        $expense->actual_expense = $request->amount;
        $expense->description = $request->description;
        $expense->save();

        return to_route('budget.index');
    }

    public function destroyIncome($id)
    {
        $income = Income::find($id);

        if ($income) {
            $income->delete();
            return to_route('budget.index');
        }
        return to_route('budget.index');
    }

    public function updateExpense(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
        ]);

        $expense = Expense::find($id);

        if ($expense) {
            $expense->expense_type = $request->category;
            $expense->actual_expense = $request->amount;
            $expense->description = $request->description;
            $expense->update();

            return to_route('budget.index');
        }

        return to_route('budget.index');
    }

    public function updateIncome(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        $income = Income::find($id);

        if ($income) {
            $income->income_type = $request->category;
            $income->actual_income = $request->amount;
            $income->update();

            return to_route('budget.index');    
        }

        return to_route('budget.index');
    }

    public function destroyExpense($id)
    {
        $expense = Expense::find($id);

        if ($expense) {
            // If the expense category is "Loans", delete from debt manager and debt_calc
            if ($expense->category == 'Loans') {
                $debt = Debt::where('user_id', auth()->id())
                    ->where('debt', $expense->actual_expense)
                    ->where('debt_name', 'Loan from Budget')
                    ->first();

                $debtCalc = Debt::where('user_id', auth()->id())
                    ->where('debt', $expense->actual_expense)
                    ->where('debt_name', 'Loan from Budget')
                    ->first();

                if ($debt) {
                    $debt->delete();
                }

                if ($debtCalc) {
                    $debtCalc->delete();
                }
            }

            $expense->delete();
            // Use Inertia::location for redirect
            return to_route('budget.index');
        }

        // Use Inertia::location for redirect - Handle error case as needed
        return to_route('budget.index');
    }
}
