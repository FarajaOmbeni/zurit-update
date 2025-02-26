<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\WithholdingTax;
use App\Models\InvestmentPlanner;
use Illuminate\Support\Facades\DB;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class InvestmentController extends Controller
{
    use NetIncomeCalculator;

    public function index()
    {
        $investments = InvestmentPlanner::where('user_id', auth()
            ->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $existing_investments = WithholdingTax::all();
        $netIncome = $this->calculateNetIncome(auth()->id());
        $t_bills = InvestmentPlanner::where('investment_type', 'Treasury Bills')->orderBy('created_at', 'desc')->get();
        $gov_bonds = InvestmentPlanner::where('investment_type', 'Government Bonds')->orderBy('created_at', 'desc')->get();
        $infra_bonds = InvestmentPlanner::where('investment_type', 'Infrastructure Bonds')->orderBy('created_at', 'desc')->get();
        $saccos = InvestmentPlanner::where('investment_type', 'Sacco Investments')->orderBy('created_at', 'desc')->get();
        $mmfs = InvestmentPlanner::where('investment_type', 'Money Market Fund')->orderBy('created_at', 'desc')->get();
        $investments_chart = InvestmentPlanner::where('user_id', auth()
            ->id())
            ->orderBy('total_investment', 'desc')
            ->get();
        $investment_values = [];
        $investment_names = [];
        $investment_months = [];

        $totalInvestments = InvestmentPlanner::where('user_id', auth()->id())->sum('total_investment');

        foreach ($investments_chart as $monthly_investment) {
            $investment_values[] = $monthly_investment->total_investment;
            $investment_names[] = $monthly_investment->investment_type;
            $investment_months[] = $monthly_investment->created_at->format('m');
        }

        return Inertia::render('UserDashboard/InvestmentPlanner', [

            'netIncome' => $netIncome,
            'investments' => $investments,
            'existing_investments' => $existing_investments,
            't_bills' => $t_bills,
            'gov_bonds' => $gov_bonds,
            'infra_bonds' => $infra_bonds,
            'saccos' => $saccos,
            'mmfs' => $mmfs,
            'investment_values' => $investment_values,
            'investment_names' => $investment_names,
            'investment_months' => $investment_months,
            'investments_chart' => $investments_chart,
            'totalInvestments' => $totalInvestments

        ]);
    }

    public function storemonthlyInvestment(Request $request)
    {
        // Retrieve only the specified fields from the request
        $investment = new InvestmentPlanner;
        $investment->investment_type = $request->investment_type;
        $investment->details_of_investment = $request->details_of_investment;
        $investment->initial_investment = $request->initial_investment;
        $investment->total_investment = $request->initial_investment + $request->total_investment + $request->monthly_contribution + $request->real_estate_price;
        $investment->number_of_months = $request->number_of_months;
        $investment->number_of_years = $request->number_of_years;
        $investment->number_of_days = $request->number_of_days;
        $investment->mmf_name = $request->mmf_name;
        $investment->rate_of_return = $request->rate_of_return;
        $investment->user_id = auth()->id();

        $expense = new Expense;
        $expense->user_id = auth()->id();
        $expense->expense_type = $request->investment_type;
        $expense->actual_expense = $request->initial_investment + $request->total_investment + $request->monthly_contribution + $request->real_estate_price;
        $expense->is_investment = 1;

        $asset = new Asset;
        $asset->user_id = auth()->id();
        $asset->asset_description = $request->investment_type;
        $asset->asset_value = $request->initial_investment + $request->total_investment + $request->monthly_contribution + $request->monthly_income;

        // Create a new Investment Planner record
        $investment->save();
        $expense->save();
        $asset->save();

        return redirect()->route('user_investmentplanner')->with('success', [
            'message' => 'Investment Created Successfully',
            'duration' => 3000,
        ]);
    }


    


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $investment = InvestmentPlanner::find($id);

            // Ensure the investment exists and the user is authorized
            if (!$investment || $investment->user_id != auth()->id()) {
                return redirect()->route('user_investmentplanner')->with('error', [
                    'message' => 'Error: Investment not found or unauthorized action',
                    'duration' => 3000,
                ]);
            }

            // Find and delete ONLY the specific asset
            $asset = Asset::where('created_at', $investment->created_at)
                ->where('asset_description', $investment->investment_type)
                ->where('user_id', auth()->id())
                ->first();

            if ($asset) {
                // Use direct query to avoid any model events
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->where('user_id', auth()->id())
                    ->delete();
            }

            // Find and delete the specific expense
            $expense = Expense::where('created_at', $investment->created_at)
                ->where('expense_type', $investment->investment_type)
                ->where('user_id', auth()->id())
                ->first();

            if ($expense) {
                $expense->delete();
            }

            // Delete the investment using direct query to avoid cascade
            DB::table('investments')->where('id', $investment->id)->delete();

            DB::commit();

            return redirect()->route('user_investmentplanner')->with('success', [
                'message' => 'Investment Deleted Successfully',
                'duration' => 3000,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user_investmentplanner')->with('error', [
                'message' => 'Error occurred while deleting investment',
                'duration' => 3000,
            ]);
        }
    }

    public function updateRate(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'update_rate' => 'required|numeric|min:0',
        ]);

        // Find the investment
        $investment = InvestmentPlanner::findOrFail($id);

        // Update rate of return
        $investment->rate_of_return = $request->update_rate;
        $investment->save();

        return redirect()->route('user_investmentplanner')->with('success', [
            'message' => 'Rate Updated Succesfully!',
            'duration' => 3000,
        ]);
    }

    public function contribute(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'contribution_amount' => 'required|numeric|min:0',
        ]);

        // Find the investment
        $investment = InvestmentPlanner::findOrFail($id);

        // Add the contribution to total_investment
        $investment->total_investment += $request->contribution_amount;
        $investment->save();

        // Find the expense that matches the debt name for the current user
        $expense = Expense::where('expense_type', $investment->title)
            ->where('user_id', auth()->id())
            ->first();

        // Check if the expense exists before updating
        if ($expense) {
            $expense->actual_expense += $request->input('contribution_amount');
            $expense->save();
        } else {
            // If the expense doesn't exist, create a new one
            Expense::create([
                'user_id' => auth()->id(),
                'expense_type' => $expense->title,
                'actual_expense' => $request->input('current'),
                'is_goal' => 1,
                // Add any other necessary fields
            ]);
        }

        // Find the expense that matches the debt name for the current user
        $asset = Asset::where('asset_description', $investment->title)
            ->where('user_id', auth()->id())
            ->first();

        // Check if the asset exists before updating
        if ($asset) {
            $asset->asset_value += $request->input('contribution_amount');
            $asset->save();
        } else {
            // If the asset doesn't exist, create a new one
            asset::create([
                'user_id' => auth()->id(),
                'asset_description' => $asset->title,
                'asset_value' => $request->input('contribution_amount'),
            ]);
        }

        return redirect()->route('user_investmentplanner')->with('success', [
            'message' => 'Amount contributed Succesfully!',
            'duration' => 3000,
        ]);
    }
}
