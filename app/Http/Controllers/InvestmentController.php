<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Expense;
use App\Models\Investment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;
use App\Models\InvestmentContribution;

class InvestmentController extends Controller
{
    use NetIncomeCalculator;

    private function storeInitialInvestment(Investment $investment, float $payment)
    {
        // Create the transaction (no budget rule, no recurrence)
        $transaction = new Transaction();
        $transaction->user_id = auth()->id();
        $transaction->type = 'expense';
        $transaction->category = 'Investment Contribution';
        $transaction->amount = $payment;
        $transaction->transaction_date = Carbon::now();
        $transaction->description = "Initial investment for {$investment->details_of_investment}";
        $transaction->save();

        // Create the expense record
        $expense = new Expense();
        $expense->user_id = $transaction->user_id;
        $expense->category = $transaction->category;
        $expense->transaction_id = $transaction->id;
        $expense->amount = $transaction->amount;
        $expense->description = $transaction->description;
        $expense->expense_date = Carbon::now();
        $expense->save();

        // Create the investment payment record
        $investmentContribution = new investmentContribution();
        $investmentContribution->investment_id = $investment->id;
        $investmentContribution->transaction_id = $transaction->id;
        $investmentContribution->amount = $payment;
        $investmentContribution->contribution_date = Carbon::now();
        $investmentContribution->save();
    }

    private function storeRecurrentExpense(Investment $investment, float $payment)
    {
        $budgetController = new BudgetController();

        // Calculate first day of next month
        $nextMonthFirstDay = Carbon::now()->addMonth()->startOfMonth();

        // build a pseudo-request for storeExpense
        $expenseRequest = new Request([
            'category'        => 'Investment',
            'amount'          => round($payment, 2),
            'description'     => "Investment for {$investment->details_of_investment}",
            'expense_date'    => $nextMonthFirstDay,
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
        $transaction->category = 'Investment Contribution';
        $transaction->amount = $payment;
        $transaction->transaction_date = $nextMonthFirstDay;
        $transaction->description = "Investment for {$investment->details_of_investment}";
        $transaction->save();

        // Create the investment payment record
        $investmentContribution = new investmentContribution();
        $investmentContribution->investment_id = $investment->id;
        $investmentContribution->transaction_id = $transaction->id;
        $investmentContribution->amount = $payment;
        $investmentContribution->contribution_date = $nextMonthFirstDay;
        $investmentContribution->save();

        $budgetController->upsertRule($transaction, $expenseRequest);

        $investment->save();
    }

    public function index()
    {
        $investments = Investment::where('user_id', auth()->id())->get();

        return Inertia::render('UserDashboard/InvestmentPlanner', [
            'investments' => $investments,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'details_of_investment' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'initial_amount' => 'required|numeric',
            'expected_return_rate' => 'required|numeric',
        ]);

        $target_date = Carbon::createFromDate($request->target_date)->addMonths($request->duration_months)->addYears($request->duration_years)->format('Y-m-d');

        $investment = new Investment();
        $investment->user_id = auth()->id();
        $investment->type = $request->type;
        $investment->details_of_investment = $request->details_of_investment;
        $investment->description = $request->description;
        $investment->initial_amount = $request->initial_amount;
        $investment->current_amount = $request->initial_amount;
        $investment->frequency_of_return = $request->frequency_of_return;
        $investment->expected_return_rate = $request->expected_return_rate;
        if ($request->details_of_investment == '91-Day Treasury Bill') {
            $investment->start_date = Carbon::now();
            $investment->target_date = Carbon::now()->addDays(91);
        }
        else if ($request->details_of_investment == '182-Day Treasury Bill') {
            $investment->start_date = Carbon::now();
            $investment->target_date = Carbon::now()->addDays(182);
        }
        else if ($request->details_of_investment == '364-Day Treasury Bill') {
            $investment->start_date = Carbon::now();
            $investment->target_date = Carbon::now()->addDays(364);
        }
        else {
            $investment->start_date = $request->start_date;
            $investment->target_date = $target_date;
        }

        $investment->save();

        $this->storeInitialInvestment($investment, $request->initial_amount);

        if ($request->boolean('commitment')) {
            $this->storeRecurrentExpense($investment, $request->committed_amount);
        }

        return to_route('invest.index');
    }

    //Update a investment in the Investment Planner View 
    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'details_of_investment' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'initial_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'target_date' => 'required|date',
            'expected_return_rate' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            // Finally, update the Investment record.
            $investment = Investment::find($request->id);
            $investment->type = $request->type;
            $investment->details_of_investment = $request->details_of_investment;
            $investment->description = $request->description;
            $investment->initial_amount = $request->initial_amount;
            $investment->start_date = $request->start_date;
            $investment->target_date = $request->target_date;
            $investment->update();
        });


        return to_route('invest.index');
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
            $transaction->category = 'Investment Contribution';
            $transaction->amount = $request->amount;
            $transaction->transaction_date = Carbon::now();
            $transaction->description = 'Invesmtent Contribution for ' . Investment::find($request->id)->type;
            $transaction->save();

            // Now create the expense record and assign the transaction id.
            $expense = new Expense();
            $expense->transaction_id = $transaction->id;
            $expense->user_id = auth()->id();
            $expense->category = 'Investment Contribution';
            $expense->description = 'Investment Contribution for ' . Investment::find($request->id)->type;
            $expense->amount = $request->amount;
            $expense->expense_date = Carbon::now();
            $expense->save();

            // Then create the investment payment (investmentContribution) record.
            $investmentContribution = new InvestmentContribution();
            $investmentContribution->investment_id = $request->id;
            $investmentContribution->transaction_id = $transaction->id;
            $investmentContribution->amount = $request->amount;
            $investmentContribution->contribution_date = Carbon::now();
            $investmentContribution->save();

            // Finally, update the Investment record.
            $investment = Investment::find($request->id);
            $investment->current_amount += $request->amount;
            $investment->update();
        });


        return to_route('budget.index');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            // First, delete the investment contribution record.
            InvestmentContribution::where('investment_id', $id)->delete();

            // Then delete the investmetn record.
            Investment::find($id)->delete();
        });

        return to_route('invest.index');
    }
}
