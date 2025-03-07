<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Debt;
use Inertia\Inertia;
use App\Models\Expense;
use App\Models\DebtPayment;
use App\Models\Transaction;
use App\Models\ExtraPayment;
use Illuminate\Http\Request;
use App\Models\MonthlyPayment;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    use NetIncomeCalculator;

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
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'initial_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        $start_date = Carbon::createFromDate($request->start_date);
        $due_date = Carbon::createFromDate($request->due_date);
        $months = round($start_date->diffInMonths($due_date));
        if ($months == 0) {
            $months = 1;
        }

        $debt = new Debt();
        $debt->user_id = auth()->id();
        $debt->name = $request->name;
        $debt->type = $request->type;
        $debt->description = $request->description;
        $debt->initial_amount = $request->initial_amount;
        $debt->interest_rate = $request->interest_rate;
        $debt->start_date = $request->start_date;
        $debt->due_date = $request->due_date;
        $debt->minimum_payment = round(($request->initial_amount + $request->initial_amount * ($request->interest_rate / 100)) / $months);
        $debt->save();

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

        DB::transaction(function () use ($request, $months) {
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
        });


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
