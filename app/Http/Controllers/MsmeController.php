<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\BusinessProfile;
use App\Models\CashflowEntry;
use App\Models\ProfitLossRecord;
use App\Models\BalanceSheetRecord;
use App\Models\PricingModel;
use App\Models\BusinessPlan;
use App\Models\FinancialProjection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MsmeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile ?? null;

        // Get financial overview data
        $financialOverview = $this->getFinancialOverview($user->id);
        
        // Get health indicators
        $healthIndicators = $this->getHealthIndicators($user->id);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($user->id);
        
        // Get quick stats
        $quickStats = $this->getQuickStats($user->id);

        return Inertia::render('MSME/Dashboard', [
            'businessProfile' => $businessProfile,
            'financialOverview' => $financialOverview,
            'healthIndicators' => $healthIndicators,
            'recentActivities' => $recentActivities,
            'quickStats' => $quickStats,
        ]);
    }

    public function businessSetup()
    {
        $user = Auth::user();
        $businessProfile = $user->businessProfile;

        return Inertia::render('MSME/BusinessSetup', [
            'businessProfile' => $businessProfile,
        ]);
    }

    public function updateBusinessProfile(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'industry_sector' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'tax_pin' => 'nullable|string',
            'vat_number' => 'nullable|string',
            'business_address' => 'nullable|string',
            'business_phone' => 'nullable|string',
            'business_email' => 'nullable|email',
            'website' => 'nullable|url',
            'employees_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'business_age_years' => 'nullable|integer|min:0',
            'primary_market' => 'nullable|string',
            'target_customers' => 'nullable|string',
            'main_products_services' => 'nullable|string',
            'business_description' => 'nullable|string',
            'operational_status' => 'nullable|string',
            'fiscal_year_start' => 'nullable|date',
            'fiscal_year_end' => 'nullable|date',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        BusinessProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return back()->with('success', 'Business profile updated successfully.');
    }

    private function getFinancialOverview($userId)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Current month cashflow
        $currentIncome = CashflowEntry::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('entry_date', [$currentMonth, Carbon::now()])
            ->sum('amount');

        $currentExpenses = CashflowEntry::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('entry_date', [$currentMonth, Carbon::now()])
            ->sum('amount');

        // Previous month for comparison
        $lastMonthIncome = CashflowEntry::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('entry_date', [$lastMonth, $currentMonth])
            ->sum('amount');

        $lastMonthExpenses = CashflowEntry::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('entry_date', [$lastMonth, $currentMonth])
            ->sum('amount');

        $netCashPosition = $currentIncome - $currentExpenses;
        $lastMonthNetCash = $lastMonthIncome - $lastMonthExpenses;

        // Get latest P&L
        $latestPL = ProfitLossRecord::where('user_id', $userId)
            ->orderBy('period_end', 'desc')
            ->first();

        // Get latest Balance Sheet
        $latestBS = BalanceSheetRecord::where('user_id', $userId)
            ->orderBy('as_of_date', 'desc')
            ->first();

        return [
            'current_income' => $currentIncome,
            'current_expenses' => $currentExpenses,
            'net_cash_position' => $netCashPosition,
            'income_growth' => $lastMonthIncome > 0 ? (($currentIncome - $lastMonthIncome) / $lastMonthIncome) * 100 : 0,
            'expense_growth' => $lastMonthExpenses > 0 ? (($currentExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100 : 0,
            'net_cash_growth' => $lastMonthNetCash != 0 ? (($netCashPosition - $lastMonthNetCash) / abs($lastMonthNetCash)) * 100 : 0,
            'latest_profit_loss' => $latestPL,
            'latest_balance_sheet' => $latestBS,
        ];
    }

    private function getHealthIndicators($userId)
    {
        $indicators = [
            'cash_flow' => 'green',
            'profitability' => 'green',
            'liquidity' => 'green',
            'solvency' => 'green',
        ];

        // Cash Flow Health
        $netCash = $this->getFinancialOverview($userId)['net_cash_position'];
        if ($netCash < 0) {
            $indicators['cash_flow'] = 'red';
        } elseif ($netCash < 10000) { // Threshold can be configurable
            $indicators['cash_flow'] = 'amber';
        }

        // Profitability Health
        $latestPL = ProfitLossRecord::where('user_id', $userId)
            ->orderBy('period_end', 'desc')
            ->first();

        if ($latestPL) {
            if ($latestPL->net_profit < 0) {
                $indicators['profitability'] = 'red';
            } elseif ($latestPL->net_profit_margin < 5) {
                $indicators['profitability'] = 'amber';
            }
        }

        // Liquidity Health (Current Ratio)
        $latestBS = BalanceSheetRecord::where('user_id', $userId)
            ->orderBy('as_of_date', 'desc')
            ->first();

        if ($latestBS) {
            $currentRatio = $latestBS->current_ratio;
            if ($currentRatio < 1) {
                $indicators['liquidity'] = 'red';
            } elseif ($currentRatio < 1.5) {
                $indicators['liquidity'] = 'amber';
            }

            // Solvency Health (Debt to Equity)
            $debtToEquity = $latestBS->debt_to_equity_ratio;
            if ($debtToEquity > 2) {
                $indicators['solvency'] = 'red';
            } elseif ($debtToEquity > 1) {
                $indicators['solvency'] = 'amber';
            }
        }

        return $indicators;
    }

    private function getRecentActivities($userId)
    {
        // Get recent cashflow entries (sort and display by creation time so newly added items always surface)
        $recentCashflow = CashflowEntry::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($entry) {
                return [
                    'type' => 'cashflow',
                    'description' => trim(ucfirst($entry->type) . ': ' . ($entry->description ?: $entry->category)),
                    'amount' => $entry->amount,
                    // Use created_at for recency so backdated entries still appear in recent activity
                    'date' => $entry->created_at,
                    'category' => $entry->category,
                ];
            });

        // Get recent pricing models
        $recentPricing = PricingModel::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($model) {
                return [
                    'type' => 'pricing',
                    'description' => 'Pricing model: ' . $model->product_service_name,
                    'amount' => $model->suggested_selling_price,
                    'date' => $model->created_at,
                ];
            });

        // Combine and sort by date (creation time)
        return $recentCashflow->concat($recentPricing)
            ->sortByDesc('date')
            ->take(8)
            ->values();
    }

    private function getQuickStats($userId)
    {
        // Total products/services priced
        $totalPricingModels = PricingModel::where('user_id', $userId)
            ->where('is_active', true)
            ->count();

        // Total business plans
        $totalBusinessPlans = BusinessPlan::where('user_id', $userId)->count();

        // Total financial projections
        $totalProjections = FinancialProjection::where('user_id', $userId)->count();

        // Average profit margin from pricing models
        $avgProfitMargin = PricingModel::where('user_id', $userId)
            ->where('is_active', true)
            ->avg('desired_profit_margin') ?: 0;

        // Monthly transactions count
        $monthlyTransactions = CashflowEntry::where('user_id', $userId)
            ->whereBetween('entry_date', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->count();

        return [
            'total_pricing_models' => $totalPricingModels,
            'total_business_plans' => $totalBusinessPlans,
            'total_projections' => $totalProjections,
            'average_profit_margin' => round($avgProfitMargin, 1),
            'monthly_transactions' => $monthlyTransactions,
        ];
    }

    public function reports()
    {
        $user = Auth::user();
        
        // Get available reports
        $profitLossReports = ProfitLossRecord::where('user_id', $user->id)
            ->orderBy('period_end', 'desc')
            ->take(12)
            ->get();

        $balanceSheetReports = BalanceSheetRecord::where('user_id', $user->id)
            ->orderBy('as_of_date', 'desc')
            ->take(12)
            ->get();

        return Inertia::render('MSME/Reports', [
            'profitLossReports' => $profitLossReports,
            'balanceSheetReports' => $balanceSheetReports,
        ]);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:profit_loss,balance_sheet,cashflow',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        switch ($request->type) {
            case 'profit_loss':
                return $this->generateProfitLossReport($user->id, $startDate, $endDate, $request->format);
            case 'balance_sheet':
                return $this->generateBalanceSheetReport($user->id, $endDate, $request->format);
            case 'cashflow':
                return $this->generateCashflowReport($user->id, $startDate, $endDate, $request->format);
        }
    }

    public function generateProfitLossReport($userId, $startDate, $endDate, $format)
    {
        // Generate or get existing P&L record
        $plRecord = ProfitLossRecord::generateFromCashflow($userId, $startDate, $endDate);
        
        // Navigate to the P&L show page (let the page handle any downloads)
        return Inertia::location(route('profit-loss.show', $plRecord->id));
    }

    public function generateBalanceSheetReport($userId, $asOfDate, $format)
    {
        $bsRecord = BalanceSheetRecord::where('user_id', $userId)
            ->where('as_of_date', '<=', $asOfDate)
            ->orderBy('as_of_date', 'desc')
            ->first();

        if (!$bsRecord) {
            // Generate a new balance sheet if none exists
            $bsRecord = BalanceSheetRecord::generateFromAssetsAndLiabilities($userId, $asOfDate);
        }

        // Navigate to the Balance Sheet show page
        return Inertia::location(route('balance-sheet.show', $bsRecord->id));
    }

    public function generateCashflowReport($userId, $startDate, $endDate, $format)
    {
        // Redirect to cashflow analytics with date filters for report viewing
        $queryParams = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        $queryParams['format'] = $format === 'pdf' ? 'pdf' : 'excel';

        // Navigate to cashflow analytics with the selected window
        return Inertia::location(route('cashflow.analytics', $queryParams));
    }
}
