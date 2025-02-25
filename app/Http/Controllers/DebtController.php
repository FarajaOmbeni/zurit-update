<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\ExtraPayment;
use Illuminate\Http\Request;
use App\Models\MonthlyPayment;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    use NetIncomeCalculator;

    private function calculateEndPeriod($current_balance, $interest_rate, $minimum_payment)
    {
        $monthly_interest_rate = $interest_rate / 12 / 100;
        $end_period = log($minimum_payment / ($minimum_payment - $current_balance * $monthly_interest_rate)) / log(1 + $monthly_interest_rate);
        return ceil($end_period);
    }

    public function store(Request $request)
    {
        $debt = new Debt;
        $debt->user_id = Auth::id();
        $debt->category = $request->debt['category']['name'] == 'other-category' ? $request->debt['other-category']['name'] : $request->debt['category']['name'];
        $debt->debt_name = $request->debt['debt_name']['name'];
        $debt->current_balance = $request->debt['current_balance']['value'];
        $debt->interest_rate = $request->debt['interest_rate']['value'];
        $debt->minimum_payment = $request->debt['minimum_payment']['value'];
        $start_period = Carbon::parse($request->input('start_date'));
        $end_period = Carbon::parse($request->input('end_date'));
        $debt->start_period = $start_period;
        $debt->end_period = $end_period;

        // Calculate months between start_date and end_date
        $num_months = $start_period->diffInMonths($end_period);
        $debt->number_of_months = $num_months;
        $debt->minimum_monthly_payment = $debt->current_balance / $num_months;
        $debt->save();

        $expense = new Expense;
        $expense->expense_type = $request->debt['debt_name']['name'];
        $expense->actual_expense = $request->debt['minimum_payment']['value']; //this is a field to keep track of expenses and if the user wants to contrubute as they record the loan
        $expense->user_id = Auth::id();
        $expense->is_loan = 1;
        $expense->save();

        return redirect('user_debtcalc')->with('success', [
            'message' => 'Debt Added Succesfully',
            'duration' => 3000,
        ]);;
    }

    public function showDebtFreeCountdown()
    {
        $debts = Debt::where('user_id', auth()->id())->orderBy('current_balance', 'desc')->get();

        $end_period = null;
        $remaining_time = null;

        if (!$debts->isEmpty()) {
            $end_periods = $debts->map(function ($debt) {
                return Carbon::parse($debt->end_period);
            });

            $end_period = $end_periods->max();
            $remaining_time = Carbon::now()->diffInMonths($end_period);
        }

        // Progress circle
        $monthlyPayments = MonthlyPayment::where('user_id', Auth::id())->get();

        // Calculate net income
        $netIncome = $this->calculateNetIncome(auth()->id());

        $debts = Debt::where('user_id', Auth::id())->get();

        $principalPaid = $monthlyPayments->sum('current_balance');

        $totalDebt = $debts->filter(function ($goal) {
            return $goal->current_balance != $goal->minimum_payment;
        })->sum('current_balance');

        $totalPaid = Debt::where('user_id', auth()->id())
            ->whereRaw('current_balance != minimum_payment')
            ->sum('minimum_payment');

        $remainingBalance = $debts->sum(function ($debt) {
            return $debt->current_balance - $debt->minimum_payment;
        });

        $principalPaid = $debts->sum('current_balance') - $remainingBalance;

        // **Calculate total monthly payments**
        $totalMonthlyPayments = Debt::where('user_id', auth()->id())->sum('minimum_monthly_payment');

        return view('user_debtcalc', [
            'end_period' => $end_period,
            'remaining_time' => $remaining_time,
            'totalDebt' => $totalDebt,
            'netIncome' => $netIncome,
            'principalPaid' => $principalPaid,
            'remainingBalance' => $remainingBalance,
            'totalPaid' => $totalPaid,
            'totalMonthlyPayments' => $totalMonthlyPayments, // Pass total monthly payments to view
        ]);
    }


    public function storeExtraPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Create a new extra payment record
        $extraPayment = new ExtraPayment;
        $extraPayment->user_id = Auth::id();
        $extraPayment->amount = $request->amount;
        $extraPayment->save();

        // Redirect back with a success message
        return back()->with('success', 'Extra payment stored successfully.');
    }

    public function storeMonthlyPayment($debtId, $amount)
    {
        $debt = Debt::find($debtId);

        $monthlyPayment = new MonthlyPayment;
        $monthlyPayment->debt_name = $debt->debt_name;
        $monthlyPayment->amount = $amount;
        $monthlyPayment->payment_date = now();
        $monthlyPayment->save();
    }

    public function getDebtProgress()
    {
        $debts = Debt::where('user_id', Auth::id())->get();

        $totalDebt = $debts->sum('current_balance');
        $remainingBalance = $debts->sum(function ($debt) {
            return $debt->current_balance - $debt->minimum_payment;
        });
        $principalPaid = $totalDebt - $remainingBalance;

        return view('user_debtcalc', [
            'totalDebt' => $totalDebt,
            'principalPaid' => $principalPaid,
            'remainingBalance' => $remainingBalance,
        ]);
    }

    public function payLoan(Request $request, $id)
    {
        // Validate the payment amount
        $request->validate([
            'pay_loan_amount' => 'required|numeric|min:1',
        ]);

        // Find the loan by ID
        $debt = Debt::findOrFail($id);

        // Calculate the new balance
        $newBalance = $debt->minimum_payment + $request->input('pay_loan_amount');

        // Find the expense that matches the debt name for the current user
        $expense = Expense::where('expense_type', $debt->debt_name)
            ->where('user_id', auth()->id())
            ->first();

        // Update the current balance
        $debt->minimum_payment = $newBalance;
        $debt->save();

        // Check if the expense exists before updating
        if ($expense) {
            $expense->actual_expense += $request->input('pay_loan_amount');
            $expense->save();
        } else {
            // If the expense doesn't exist, create a new one
            Expense::create([
                'user_id' => auth()->id(),
                'expense_type' => $debt->debt_name,
                'actual_expense' => $request->input('pay_loan_amount'),
                // Add any other necessary fields
            ]);
        }

        // Compare expenses with debt calculations and delete matching expenses only
        $loanExpenseComparison = Expense::where('expenses.user_id', auth()->id())
            ->join('debt_calc', function ($join) {
                $join->on('expenses.expense_type', '=', 'debt_calc.debt_name')
                    ->where('debt_calc.user_id', '=', auth()->id());
            })
            ->select('expenses.id', 'expenses.actual_expense', 'debt_calc.current_balance')
            ->get();

        foreach ($loanExpenseComparison as $comparison) {
            if ($comparison->actual_expense >= $comparison->current_balance) {
                // Only delete from expenses table
                Expense::where('id', $comparison->id)->delete();
            }
        }

        // Redirect back with a success message
        return redirect('user_debtcalc')->with('success', [
            'message' => 'Debt Paid Succesfully',
            'duration' => 3000,
        ]);
    }
}
