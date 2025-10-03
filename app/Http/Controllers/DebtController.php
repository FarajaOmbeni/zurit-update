<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Debt;
use Inertia\Inertia;
use App\Models\Expense;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BudgetController;

class DebtController extends Controller
{
    use NetIncomeCalculator;

    private function storeRecurrentExpense(Debt $debt, float $payment, bool $isRecurring = true)
    {
        // Only create expense and payment records if recurring is enabled
        if (!$isRecurring) {
            return; // Don't create any expense or payment records for non-recurring debts
        }

        $budgetController = new BudgetController();

        // build a pseudo-request for storeExpense
        $expenseRequest = new Request([
            'category'        => 'Loan Repayment',
            'amount'          => round($payment, 2),
            'description'     => "Debt Repayment for {$debt->name}",
            'expense_date'    => today(),
            'is_recurring'    => $isRecurring,
            'recurrence_pattern' => 'monthly',
        ]);

        $expenseRequest->setUserResolver(function () {
            return auth()->user(); // ensure auth()->id() works inside called controller
        });

        // Create the transaction
        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->type = 'expense';
        $transaction->category = 'Debt Payment';
        $transaction->amount = $payment;
        $transaction->transaction_date = now();
        $transaction->description = "Debt Payment for {$debt->name}";
        $transaction->save();

        // Create the expense record
        $expense = new Expense();
        $expense->user_id = $transaction->user_id;
        $expense->category = $transaction->category;
        $expense->transaction_id = $transaction->id;
        $expense->amount = $transaction->amount;
        $expense->description = $transaction->description;
        $expense->expense_date = now();
        $expense->save();

        // Create the debt payment record
        $debtPayment = new DebtPayment();
        $debtPayment->debt_id = $debt->id;
        $debtPayment->transaction_id = $transaction->id;
        $debtPayment->amount = $payment;
        $debtPayment->payment_date = now();
        $debtPayment->save();

        $budgetController->upsertRule($transaction, $expenseRequest);

        // Update debt
        $debt->current_amount += $payment;
        if ($debt->current_amount >= $debt->initial_amount) {
            $debt->status = 'paid_off';
        }
        $debt->save();
    }

    public function index()
    {
        $debts = Debt::where('user_id', Auth::id())->get();

        return Inertia::render('UserDashboard/DebtManager', [
            'debts' => $debts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'description'    => 'required|string|max:255',
            'initial_amount' => 'required|numeric',
            'interest_rate'  => 'required|numeric',
            'start_date'     => 'required|date',
            'duration_months' => 'required|numeric',
            'duration_years' => 'required|numeric',
            'is_recurring'   => 'boolean',
        ]);

        $start_date = Carbon::createFromDate($request->start_date);
        $due_date   = Carbon::createFromDate($request->start_date)->addMonths($request->duration_months)->addYears($request->duration_years)->format('Y-m-d');

        // Calculate the number of months between start and due
        $months = $start_date->diffInMonths($due_date);
        $months = $months ?: 1;  // ensure at least 1 month

        // Amortization parameters
        $P = $request->initial_amount;
        $annualRate = $request->interest_rate;
        $r_monthly  = ($annualRate / 100) / 12;
        $n          = $months;

        if ($r_monthly > 0) {
            $payment = ($P * $r_monthly) / (1 - pow(1 + $r_monthly, -$n));
        } else {
            // zero-interest case
            $payment = $P / $n;
        }

        $debt = new Debt();
        $debt->user_id         = auth()->id();
        $debt->name            = $request->name;
        $debt->type            = $request->type;
        $debt->description     = $request->description;
        $debt->initial_amount  = $P;
        $debt->interest_rate   = $annualRate;
        $debt->start_date      = $request->start_date;
        $debt->due_date        = $due_date;
        $debt->minimum_payment = round($payment, 2);       // currency-friendly rounding
        $debt->save();

        $this->storeRecurrentExpense($debt, $payment, $request->boolean('is_recurring'));

        return to_route('debt.index', [
            'newDebt' => $debt,
        ]);
    }


    //Update a debt in the Debt Manager View 
    public function update(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string|max:255',
            'description'    => 'required|string|max:255',
            'initial_amount' => 'required|numeric',
            'interest_rate'  => 'required|numeric',
            'start_date'     => 'required|date',
            'due_date'       => 'required|date',
        ]);

        $start_date = Carbon::createFromDate($request->start_date);
        $due_date   = Carbon::createFromDate($request->due_date);
        $months     = round($start_date->diffInMonths($due_date));
        if ($months == 0) {
            $months = 1;
        }

        $debt = DB::transaction(function () use ($request, $months) {
            // Finally, update the Debt record.
            $debt = Debt::find($request->id);
            $debt->name = $request->name;
            $debt->type = $request->type;
            $debt->description = $request->description;
            $debt->initial_amount = $request->initial_amount;
            $debt->interest_rate = $request->interest_rate;
            $debt->start_date = $request->start_date;
            $debt->due_date = $request->due_date;
            $debt->minimum_payment = round(
                ($request->initial_amount + $request->initial_amount * ($request->interest_rate / 100)) / $months
            );
            $debt->update();

            return $debt;
        });

        $this->storeRecurrentExpense($debt, $debt->minimum_payment, $request->boolean('is_recurring', true));

        return to_route('debt.index');
    }

    public function contribute(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            // Create the transaction record first.
            $transaction = new Transaction();
            $transaction->user_id = auth()->id();
            $transaction->type = 'expense';
            $transaction->category = 'Debt Payment';
            $transaction->amount = $request->amount;
            $transaction->transaction_date = Carbon::now();
            $transaction->description = 'Debt Payment for ' . Debt::find($request->id)->name;
            $transaction->save();

            // Now create the expense record and assign the transaction id.
            $expense = new Expense();
            $expense->transaction_id = $transaction->id;
            $expense->user_id = auth()->id();
            $expense->category = 'Debt Payment';
            $expense->description = 'Debt Payment for ' . Debt::find($request->id)->name;
            $expense->amount = $request->amount;
            $expense->expense_date = Carbon::now();
            $expense->save();

            // Then create the debt payment (debtContribution) record.
            $debtPayment = new DebtPayment();
            $debtPayment->debt_id = $request->id;
            $debtPayment->transaction_id = $transaction->id;
            $debtPayment->amount = $request->amount;
            $debtPayment->payment_date = Carbon::now();
            $debtPayment->save();

            // Finally, update the Debt record.
            $debt = Debt::find($request->id);
            $debt->current_amount += $request->amount;
            if ($debt->current_amount >= $debt->initial_amount) {
                $debt->status = 'paid_off';
            }
            $debt->update();
        });


        return to_route('budget.index');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            // First, delete the debt payment record.
            DebtPayment::where('debt_id', $id)->delete();

            // Then delete the debt record.
            Debt::find($id)->delete();
        });

        return to_route('debt.index');
    }
}
