<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Goal;
use Inertia\Inertia;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GoalContribution;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BudgetController;

class GoalController extends Controller
{
    use NetIncomeCalculator;

    private function storeRecurrentExpense(Goal $goal, float $payment)
    {
        $budgetController = new BudgetController();

        // build a pseudo-request for storeExpense
        $expenseRequest = new Request([
            'category'        => 'Goal Contribution',
            'amount'          => round($payment, 2),
            'description'     => "Goal Contribution for {$goal->name}",
            'expense_date'    => today(),
            'is_recurring'    => true,
            'recurrence_pattern' => 'monthly',
        ]);

        $expenseRequest->setUserResolver(function () {
            return auth()->user(); // ensure auth()->id() works inside called controller
        });

        // Create the transaction
        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->type = 'expense';
        $transaction->category = 'Goal Contribution';
        $transaction->amount = $payment;
        $transaction->transaction_date = now();
        $transaction->description = "Goal Contribution for {$goal->name}";
        $transaction->save();

        // Create the goal payment record
        $goalContribution = new GoalContribution();
        $goalContribution->goal_id = $goal->id;
        $goalContribution->transaction_id = $transaction->id;
        $goalContribution->amount = $payment;
        $goalContribution->contribution_date = now();
        $goalContribution->save();

        $budgetController->upsertRule($transaction, $expenseRequest);

        // Update goal
        $goal->current_amount += $payment;
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->status = 'achieved';
        }
        $goal->save();
    }

    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->get();

        return Inertia::render('UserDashboard/GoalSetting', [
            'goals' => $goals,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'target_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'duration_months' => 'required|integer',
            'duration_years' => 'required|integer',
        ]);

        $start_date = Carbon::createFromDate($request->start_date);
        $target_date = Carbon::createFromDate($request->target_date)->addMonths($request->duration_months)->addYears($request->duration_years)->format('Y-m-d');

        $months = round($start_date->diffInMonths($target_date));
        if ($months == 0) {
            $months = 1;
        }

        $goal = new Goal();
        $goal->user_id = auth()->id();
        $goal->name = $request->name;
        $goal->description = $request->description;
        $goal->target_amount = $request->target_amount;
        $goal->start_date = $request->start_date;
        $goal->target_date = $target_date;
        $goal->minimum_payment = round($request->target_amount / $months);
        $goal->save();

        if ($request->boolean('commitment')) {
            $this->storeRecurrentExpense($goal, $goal->minimum_payment);
        }

        return to_route('goal.index');
    }

    //Update a goal in the Goal Setting View 
    public function update(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string|max:255',
            'target_amount' => 'required|numeric',
            'start_date'     => 'required|date',
        ]);

        $start_date = Carbon::createFromDate($request->start_date);
        $target_date   = Carbon::createFromDate($request->target_date);
        $months     = round($start_date->diffInMonths($target_date));
        if ($months == 0) {
            $months = 1;
        }

        DB::transaction(function () use ($request, $months) {
            // Finally, update the Goal record.
            $goal = Goal::find($request->id);
            $goal->name = $request->name;
            $goal->description = $request->description;
            $goal->target_amount = $request->target_amount;
            $goal->start_date = $request->start_date;
            $goal->target_date = $request->target_date;
            $goal->minimum_payment = round          ($request->target_amount / $months);
            $goal->update();

            if ($request->boolean('commitment')) {
                $this->storeRecurrentExpense($goal, $goal->minimum_payment);
            }
        });


        return to_route('goal.index');
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
            $transaction->category = 'Goal Contribution';
            $transaction->amount = $request->amount;
            $transaction->transaction_date = Carbon::now();
            $transaction->description = 'Goal Contribution for ' . Goal::find($request->id)->name;
            $transaction->save();

            // Now create the expense record and assign the transaction id.
            $expense = new Expense();
            $expense->transaction_id = $transaction->id;
            $expense->user_id = auth()->id();
            $expense->category = 'Goal Contribution';
            $expense->description = 'Goal Contribution for ' . Goal::find($request->id)->name;
            $expense->amount = $request->amount;
            $expense->expense_date = Carbon::now();
            $expense->save();

            // Then create the goal payment (goalContribution) record.
            $goalContribution = new GoalContribution();
            $goalContribution->goal_id = $request->id;
            $goalContribution->transaction_id = $transaction->id;
            $goalContribution->amount = $request->amount;
            $goalContribution->contribution_date = Carbon::now();
            $goalContribution->save();

            // Finally, update the Goal record.
            $goal = Goal::find($request->id);
            $goal->current_amount += $request->amount;
            if ($goal->current_amount >= $goal->target_amount) {
                $goal->status = 'achieved';
            }
            $goal->update();
        });


        return to_route('budget.index');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            // First, delete the goal payment record.
            GoalContribution::where('goal_id', $id)->delete();

            // Then delete the goal record.
            Goal::find($id)->delete();
        });

        return to_route('goal.index');
    }
}
