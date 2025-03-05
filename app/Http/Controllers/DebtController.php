<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\ExtraPayment;
use Illuminate\Http\Request;
use App\Models\MonthlyPayment;
use App\Models\Transaction;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

        return to_route('debt.index');
    }


    public function update(Request $request) {
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

        $debt = Debt::find($request->id);
        $debt->name = $request->name;
        $debt->type = $request->type;
        $debt->description = $request->description;
        $debt->initial_amount = $request->initial_amount;
        $debt->interest_rate = $request->interest_rate;
        $debt->start_date = $request->start_date;
        $debt->due_date = $request->due_date;
        $debt->minimum_payment = round(($request->initial_amount + $request->initial_amount * ($request->interest_rate / 100)) / $months);
        $debt->update();

        return to_route('debt.index');
    }

    public function contribute(Request $request) {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $debt = Debt::find($request->id);
        $debt->current_amount += $request->amount;
        if ($debt->current_amount >= $debt->initial_amount) {
            $debt->status = 'paid_off';
        }
        $debt->update();

        Expense::create([
            'user_id' => Auth::id(),
            'category_id' => 9,
            'description' => 'Debt Payment for '. $debt->name,
            'amount' => $request->amount,
            'expense_date' => Carbon::now(),
        ]);

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => 9,
            'type' => 'expense',
            'amount' => $request->amount,
            'description' => 'Payment for ' . $debt->name,
            'transaction_date' => Carbon::now(),
        ]);

        DebtPayment::create([
            'debt_id' => $request->id,
            'transaction_id' => Transaction::latest()->first()->id,
            'amount' => $request->amount,
            'notes' => 'Regular payment',
            'payment_date' => Carbon::now(),
        ]);

        return to_route('budget.index');
    }
}
