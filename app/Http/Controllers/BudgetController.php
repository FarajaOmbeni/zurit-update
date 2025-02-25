<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\BudgetPlanner;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class BudgetController extends Controller
{
    use NetIncomeCalculator;

    public function index()
    {
        // Get current month and year using Carbon
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Check if there is data for the current month
        $hasDataForCurrentMonth = isset($monthlyIncomes[$currentMonth]) && isset($monthlyExpenses[$currentMonth]);

        // Fetch all incomes for the currently logged-in user
        $income = Income::where('user_id', auth()->id())->get();

        // Fetch all expenses for the currently logged-in user
        $expenses = Expense::where('user_id', auth()->id())->get();
        // Find the expense that matches the debt name for the current user

        // Combine the incomes and expenses into the budget data
        $budget = $income->concat($expenses);

        // Calculate net income
        $actualIncome = Income::where('user_id', auth()->id())->sum('actual_income');
        $actualExpenses = Expense::where('user_id', auth()->id())->sum('actual_expense');

        // Calculate net income
        $netIncome = $this->calculateNetIncome(auth()->id());

        // Calculate monthly incomes and expenses
        $monthlyIncomes = Income::where('user_id', auth()->id())
            ->selectRaw('SUM(actual_income) as total, MONTH(created_at) as month')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        $monthlyExpenses = Expense::where('user_id', auth()->id())
            ->selectRaw('SUM(actual_expense) as total, MONTH(created_at) as month')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        // Get current month
        $currentMonth = date('m');

        // Check if there is data for the current month
        $hasDataForCurrentMonth = isset($monthlyIncomes[$currentMonth]) && isset($monthlyExpenses[$currentMonth]);


        return Inertia::render('UserDashboard/BudgetPlanner', [
            'budget' => $budget,
            'income' => $income,
            'expenses' => $expenses,
            'actualIncome' => $actualIncome,
            'actualExpenses' => $actualExpenses,
            'netIncome' => $netIncome,
            'monthlyIncomes' => $monthlyIncomes,
            'monthlyExpenses' => $monthlyExpenses,
            'hasDataForCurrentMonth' => $hasDataForCurrentMonth,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'income_type' => 'required|string|max:255',
            'income' => 'required|numeric',
        ]);

        // Calculate net income
        $actualIncome = Income::where('user_id', auth()->id())->sum('actual_income');
        $actualExpenses = Expense::where('user_id', auth()->id())->sum('actual_expense');
        $netIncome = $actualIncome - $actualExpenses;

        $income = new Income();
        $income->user_id = auth()->id();
        $income->income_type = $request->income_type;
        $income->actual_income = $request->income;

        $income->save();

        return redirect('user_budgetplanner')->with('success', [
            'message' => 'Income added Successfully!',
            'duration' => 3000,
        ]);
    }





    public function storeExpense(Request $request)
    {
        $request->validate([
            'expense_type' => 'required|string|max:255',
            'expense' => 'required|numeric',
        ]);

        $expense = new Expense();
        $expense->user_id = auth()->id();
        $expense->expense_type = $request->expense_type;
        $expense->actual_expense = $request->expense;
        $expense->description = $request->description;

        $expense->save();

        return redirect('user_budgetplanner')->with('success', [
            'message' => 'Expense added Successfully!',
            'duration' => 3000,
        ]);
    }

    public function destroyIncome($id)
    {
        $income = Income::find($id);

        if ($income) {
            $income->delete();
            return redirect()->route('user_budgetplanner')->with('success', [
                'message' => 'Income deleted Successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect()->route('user_budgetplanner')->with('error', [
            'message' => 'Error Deleting Income ',
            'duration' => 3000,
        ]);
    }

    public function updateExpense(Request $request, $id)
    {
        $request->validate([
            'expense_type' => 'required|string|max:255',
            'actual_expense' => 'required|numeric',
        ]);

        $expense = Expense::find($id);

        if ($expense) {
            $expense->expense_type = $request->expense_type;
            $expense->actual_expense = $request->actual_expense;
            $expense->save();

            return redirect()->route('user_budgetplanner')->with('success', [
                'message' => 'Expense updated successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect()->route('user_budgetplanner')->with('error', [
            'message' => 'Error updating expense',
            'duration' => 3000,
        ]);
    }
    public function updateIncome(Request $request, $id)
    {
        $request->validate([
            'income_type' => 'required|string|max:255',
            'actual_income' => 'required|numeric',
        ]);

        $income = Income::find($id);

        if ($income) {
            $income->income_type = $request->income_type;
            $income->actual_income = $request->actual_income;

            $income->save();

            return redirect()->route('user_budgetplanner')->with('success', [
                'message' => 'Income updated successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect()->route('user_budgetplanner')->with('error', [
            'message' => 'Error updating expense',
            'duration' => 3000,
        ]);
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

                $debtCalc = DebtCalc::where('user_id', auth()->id())
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
            return redirect()->route('user_budgetplanner')->with('success', [
                'message' => 'Expense Deleted Successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect()->route('user_budgetplanner')->with('error', [
            'message' => 'Error Deleting Expense ',
            'duration' => 3000,
        ]);
    }
}
