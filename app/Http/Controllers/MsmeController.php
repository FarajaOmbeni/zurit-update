<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\BusinessProfile;
use App\Models\CashflowEntry;
use App\Models\ProfitLossRecord;
use App\Models\BalanceSheetRecord;
use App\Models\MonthEndClosure;
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
        // Render a simple page with only the parameter selection form
        return Inertia::render('MSME/Reports');
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:profit_loss,balance_sheet,cashflow',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            // For on-page viewing, format can be omitted; when provided, allow pdf/excel
            'format' => 'nullable|in:pdf,excel',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        switch ($request->type) {
            case 'profit_loss':
                if ($request->filled('format')) {
                    return $this->generateProfitLossReport($user->id, $startDate, $endDate, $request->format);
                }
                $plRecord = ProfitLossRecord::generateFromCashflow($user->id, $startDate, $endDate);

                // If a month-end closure exists for this period, use its COGS to reflect inventory adjustments
                $periodStart = $startDate->copy()->startOfMonth();
                $periodEnd = $endDate->copy()->endOfMonth();
                $closure = MonthEndClosure::where('user_id', $user->id)
                    ->where('period_start', $periodStart->toDateString())
                    ->where('period_end', $periodEnd->toDateString())
                    ->first();
                if ($closure) {
                    $plRecord->cost_of_goods_sold = (float) ($closure->calculated_cogs ?? 0);
                    // Recompute derived fields for display (do not persist here)
                    $plRecord->gross_profit = (float) $plRecord->revenue - (float) $plRecord->cost_of_goods_sold;
                    $plRecord->net_profit = (float) $plRecord->revenue - (
                        (float) $plRecord->cost_of_goods_sold +
                        (float) ($plRecord->operating_expenses ?? 0) +
                        (float) ($plRecord->tax_expense ?? 0) +
                        (float) ($plRecord->interest_expense ?? 0) +
                        (float) ($plRecord->depreciation ?? 0)
                    );
                }
                return Inertia::render('MSME/Reports', [
                    'inlineResult' => [
                        'type' => 'profit_loss',
                        'period' => [
                            'start_date' => $startDate->toDateString(),
                            'end_date' => $endDate->toDateString(),
                        ],
                        'profitLossRecord' => $plRecord,
                    ],
                ]);
            case 'balance_sheet':
                if ($request->filled('format')) {
                    return $this->generateBalanceSheetReport($user->id, $endDate, $request->format);
                }
                $bsRecord = BalanceSheetRecord::where('user_id', $user->id)
                    ->where('as_of_date', '<=', $endDate)
                    ->orderBy('as_of_date', 'desc')
                    ->first();
                if (!$bsRecord) {
                    $bsRecord = BalanceSheetRecord::generateFromAssetsAndLiabilities($user->id, $endDate);
                }
                return Inertia::render('MSME/Reports', [
                    'inlineResult' => [
                        'type' => 'balance_sheet',
                        'period' => [
                            'as_of_date' => $endDate->toDateString(),
                        ],
                        'balanceSheetRecord' => $bsRecord,
                    ],
                ]);
            case 'cashflow':
                if ($request->filled('format')) {
                    return $this->generateCashflowReport($user->id, $startDate, $endDate, $request->format);
                }
                $totalIncome = CashflowEntry::where('user_id', $user->id)
                    ->where('type', 'income')
                    ->whereBetween('entry_date', [$startDate, $endDate])
                    ->sum('amount');
                $totalExpenses = CashflowEntry::where('user_id', $user->id)
                    ->where('type', 'expense')
                    ->whereBetween('entry_date', [$startDate, $endDate])
                    ->sum('amount');
                return Inertia::render('MSME/Reports', [
                    'inlineResult' => [
                        'type' => 'cashflow',
                        'period' => [
                            'start_date' => $startDate->toDateString(),
                            'end_date' => $endDate->toDateString(),
                        ],
                        'cashflowSummary' => [
                            'total_income' => $totalIncome,
                            'total_expenses' => $totalExpenses,
                            'net' => $totalIncome - $totalExpenses,
                        ],
                    ],
                ]);
        }
    }

    public function generateProfitLossReport($userId, $startDate, $endDate, $format)
    {
        // Generate or get existing P&L record
        $plRecord = ProfitLossRecord::generateFromCashflow($userId, $startDate, $endDate);
        
        // If a format was requested, trigger a download; otherwise show the record
        if (in_array($format, ['pdf', 'excel'])) {
            // Use Inertia::location to force a full-page visit for file downloads
            return Inertia::location(route('profit-loss.download', [
                'profitLossRecord' => $plRecord->id,
                'format' => $format,
            ]));
        }

        return redirect()->route('profit-loss.show', $plRecord->id);
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

        // If a format was requested, trigger a download; otherwise show the record
        if (in_array($format, ['pdf', 'excel'])) {
            // Use Inertia::location to force a full-page visit for file downloads
            return Inertia::location(route('balance-sheet.download', [
                'balanceSheetRecord' => $bsRecord->id,
                'format' => $format,
            ]));
        }

        return redirect()->route('balance-sheet.show', $bsRecord->id);
    }

    public function generateCashflowReport($userId, $startDate, $endDate, $format)
    {
        // Redirect to cashflow analytics with date filters for report viewing
        $queryParams = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        $queryParams['format'] = $format === 'pdf' ? 'pdf' : 'excel';

        // Redirect to cashflow analytics with the selected window
        return redirect()->route('cashflow.analytics', $queryParams);
    }

    // New: Stream a CSV summary for Profit & Loss (totals only) without persisting records
    public function downloadProfitLossSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Totals
        $totalIncome = CashflowEntry::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = CashflowEntry::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->sum('amount');

        // Category breakdowns
        $incomeByCategory = CashflowEntry::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->pluck('total', 'category');

        $expensesByCategory = CashflowEntry::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->pluck('total', 'category');

        // Friendly category names (mirrors CashflowController::getCategories())
        $friendlyCategoryNames = [
            'income' => [
                'sales_revenue' => 'Sales Revenue',
                'service_income' => 'Service Income',
                'consulting_fees' => 'Consulting Fees',
                'commission_income' => 'Commission Income',
                'rental_income' => 'Rental Income',
                'interest_income' => 'Interest Income',
                'other_income' => 'Other Income',
            ],
            'expense' => [
                'raw_materials' => 'Raw Materials',
                'inventory' => 'Inventory',
                'direct_labor' => 'Direct Labor',
                'rent' => 'Rent',
                'utilities' => 'Utilities',
                'salaries' => 'Salaries & Wages',
                'marketing' => 'Marketing & Advertising',
                'administrative' => 'Administrative',
                'insurance' => 'Insurance',
                'fuel_transport' => 'Fuel & Transport',
                'office_supplies' => 'Office Supplies',
                'equipment' => 'Equipment',
                'maintenance' => 'Maintenance & Repairs',
                'professional_services' => 'Professional Services',
                'taxes' => 'Taxes',
                'interest' => 'Interest Payments',
                'other_expenses' => 'Other Expenses',
            ],
        ];

        $fmtMoney = function ($v) {
            return number_format((float)$v, 2, '.', '');
        };

        // Define groupings for P&L-style presentation
        $cogsCategories = ['raw_materials', 'inventory', 'direct_labor'];
        $taxCategory = 'taxes';
        $interestCategory = 'interest';

        // Compute subgroup totals
        $totalCogs = 0.0;
        foreach ($cogsCategories as $cat) {
            $totalCogs += (float)($expensesByCategory[$cat] ?? 0);
        }
        $operatingExpensesTotal = 0.0;
        foreach ($expensesByCategory as $cat => $val) {
            if (!in_array($cat, $cogsCategories, true) && $cat !== $taxCategory && $cat !== $interestCategory) {
                $operatingExpensesTotal += (float)$val;
            }
        }
        $taxTotal = (float)($expensesByCategory[$taxCategory] ?? 0);
        $interestTotal = (float)($expensesByCategory[$interestCategory] ?? 0);

        $grossProfit = (float)$totalIncome - (float)$totalCogs;
        $operatingProfit = $grossProfit - $operatingExpensesTotal;
        $netProfit = $operatingProfit - $taxTotal - $interestTotal;

        // Build detailed CSV with sections
        $csv = [];
        $csv[] = ['Profit & Loss Statement'];
        $csv[] = ['Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')];
        $csv[] = [''];

        // Revenue section
        $csv[] = ['Revenue'];
        foreach ($incomeByCategory as $cat => $total) {
            $name = $friendlyCategoryNames['income'][$cat] ?? (is_null($cat) || $cat === '' ? 'Uncategorized' : (string)$cat);
            $csv[] = [$name, $fmtMoney($total)];
        }
        $csv[] = ['Total Revenue', $fmtMoney($totalIncome)];
        $csv[] = [''];

        // COGS section
        $csv[] = ['Cost of Goods Sold'];
        foreach ($cogsCategories as $cat) {
            $amount = (float)($expensesByCategory[$cat] ?? 0);
            if ($amount !== 0.0) {
                $name = $friendlyCategoryNames['expense'][$cat] ?? $cat;
                $csv[] = [$name, $fmtMoney($amount)];
            }
        }
        $csv[] = ['Total COGS', $fmtMoney($totalCogs)];
        $csv[] = ['Gross Profit', $fmtMoney($grossProfit)];
        $csv[] = ['Gross Margin (%)', $totalIncome > 0 ? number_format(($grossProfit / $totalIncome) * 100, 2, '.', '') : '0.00'];
        $csv[] = [''];

        // Operating expenses section (exclude COGS, taxes, interest)
        $csv[] = ['Operating Expenses'];
        foreach ($expensesByCategory as $cat => $total) {
            if (!in_array($cat, $cogsCategories, true) && $cat !== $taxCategory && $cat !== $interestCategory) {
                $name = $friendlyCategoryNames['expense'][$cat] ?? (is_null($cat) || $cat === '' ? 'Uncategorized' : (string)$cat);
                $csv[] = [$name, $fmtMoney($total)];
            }
        }
        $csv[] = ['Total Operating Expenses', $fmtMoney($operatingExpensesTotal)];
        $csv[] = ['Operating Profit', $fmtMoney($operatingProfit)];
        $csv[] = [''];

        // Other expenses
        $csv[] = ['Tax Expense', $fmtMoney($taxTotal)];
        $csv[] = ['Interest Expense', $fmtMoney($interestTotal)];
        $csv[] = [''];

        // Bottom line
        $csv[] = ['Net Profit', $fmtMoney($netProfit)];
        $csv[] = ['Net Profit Margin (%)', $totalIncome > 0 ? number_format(($netProfit / $totalIncome) * 100, 2, '.', '') : '0.00'];

        $output = '';
        foreach ($csv as $row) {
            // Basic CSV escaping for commas/quotes
            $escaped = array_map(function ($field) {
                $s = (string)$field;
                $needsQuotes = preg_match('/[",\n\r]/', $s);
                $s = str_replace('"', '""', $s);
                return $needsQuotes ? '"' . $s . '"' : $s;
            }, $row);
            $output .= implode(',', $escaped) . "\n";
        }

        $filename = 'profit_loss_detailed_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.csv';

        return response($output)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
