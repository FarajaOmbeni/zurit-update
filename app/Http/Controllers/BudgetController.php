<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Auth;
use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\NetIncomeCalculator;
use Inertia\Inertia;

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
                    ->with('category')
                    ->whereYear('transaction_date', $currentYear);
            },
            'expenses' => function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('expense_date', $currentMonth)
                    ->whereYear('expense_date', $currentYear);
            },
            'goals',
            'debts',
            'investments',
        ]);

        $incomeCategories = Category::where('type', 'income')
        ->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhereNull('user_id');
        })
            ->get();

        $expenseCategories = Category::where('type', 'expense')
        ->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhereNull('user_id');
        })
            ->get();


        $data = [
            'incomes'     => $user->incomes,
            'expenses'    => $user->expenses,
            'goals'       => $user->goals,
            'debts'       => $user->debts,
            'investments' => $user->investments,
            'transactions' => $user->transactions,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ];

        return Inertia::render('UserDashboard/BudgetPlanner', [
            'data' => $data,
            'currentMonth' => $currentMonthString,
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
