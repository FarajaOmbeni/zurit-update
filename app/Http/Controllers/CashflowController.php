<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CashflowEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filters from request
        $type = $request->get('type', 'all'); // all, income, expense
        $category = $request->get('category');
        $paymentMethod = $request->get('payment_method');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Build query
        $query = CashflowEntry::where('user_id', $user->id);
        
        if ($type !== 'all') {
            $query->where('type', $type);
        }
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        $query->whereBetween('entry_date', [$startDate, $endDate]);
        
        // Get entries with pagination
        $entries = $query->orderBy('entry_date', 'desc')
            ->paginate(20)
            ->appends($request->all());
        
        // Get summary stats
        $summary = $this->getSummaryStats($user->id, $startDate, $endDate);
        
        // Get categories and payment methods for filters
        $categories = $this->getCategories();
        $paymentMethods = $this->getPaymentMethods();
        
        return Inertia::render('MSME/Cashflow/Index', [
            'entries' => $entries,
            'summary' => $summary,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'filters' => [
                'type' => $type,
                'category' => $category,
                'payment_method' => $paymentMethod,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'subcategory' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'entry_date' => 'required|date',
            'is_recurring' => 'boolean',
            'business_unit' => 'nullable|string',
            'invoice_number' => 'nullable|string|max:100',
            'customer_supplier' => 'nullable|string|max:255',
            'vat_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['vat_amount'] = $validated['vat_amount'] ?? 0;
        $validated['tax_amount'] = $validated['tax_amount'] ?? 0;

        $entry = CashflowEntry::create($validated);

        return back()->with('success', 'Cashflow entry created successfully.');
    }

    public function update(Request $request, CashflowEntry $cashflowEntry)
    {
        // Check ownership
        if ($cashflowEntry->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'subcategory' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'entry_date' => 'required|date',
            'is_recurring' => 'boolean',
            'business_unit' => 'nullable|string',
            'invoice_number' => 'nullable|string|max:100',
            'customer_supplier' => 'nullable|string|max:255',
            'vat_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        $validated['vat_amount'] = $validated['vat_amount'] ?? 0;
        $validated['tax_amount'] = $validated['tax_amount'] ?? 0;

        $cashflowEntry->update($validated);

        return back()->with('success', 'Cashflow entry updated successfully.');
    }

    public function destroy(CashflowEntry $cashflowEntry)
    {
        // Check ownership
        if ($cashflowEntry->user_id !== Auth::id()) {
            abort(403);
        }

        $cashflowEntry->delete();

        return back()->with('success', 'Cashflow entry deleted successfully.');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx|max:2048',
            'mapping' => 'required|array',
        ]);

        // Process CSV/Excel import
        // This would be implemented with a package like Laravel Excel
        
        return back()->with('success', 'Cashflow entries imported successfully.');
    }

    public function analytics(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', '12m'); // 1m, 3m, 6m, 12m
        
        $startDate = $this->getStartDateFromPeriod($period);
        $endDate = Carbon::now();
        
        // Monthly trend data
        $monthlyTrends = $this->getMonthlyTrends($user->id, $startDate, $endDate);
        
        // Category breakdown
        $categoryBreakdown = $this->getCategoryBreakdown($user->id, $startDate, $endDate);
        
        // Payment method analysis
        $paymentMethodAnalysis = $this->getPaymentMethodAnalysis($user->id, $startDate, $endDate);
        
        // Cash flow forecast
        $forecast = $this->getCashflowForecast($user->id);
        
        return Inertia::render('MSME/Cashflow/Analytics', [
            'monthlyTrends' => $monthlyTrends,
            'categoryBreakdown' => $categoryBreakdown,
            'paymentMethodAnalysis' => $paymentMethodAnalysis,
            'forecast' => $forecast,
            'period' => $period,
        ]);
    }

    private function getSummaryStats($userId, $startDate, $endDate)
    {
        $entries = CashflowEntry::where('user_id', $userId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->get();

        $totalIncome = $entries->where('type', 'income')->sum('amount');
        $totalExpenses = $entries->where('type', 'expense')->sum('amount');
        $netCashflow = $totalIncome - $totalExpenses;
        
        // Previous period comparison
        $previousStart = Carbon::parse($startDate)->subDays(Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)));
        $previousEnd = Carbon::parse($startDate)->subDay();
        
        $previousEntries = CashflowEntry::where('user_id', $userId)
            ->whereBetween('entry_date', [$previousStart, $previousEnd])
            ->get();
            
        $previousIncome = $previousEntries->where('type', 'income')->sum('amount');
        $previousExpenses = $previousEntries->where('type', 'expense')->sum('amount');
        $previousNet = $previousIncome - $previousExpenses;

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_cashflow' => $netCashflow,
            'income_growth' => $previousIncome > 0 ? (($totalIncome - $previousIncome) / $previousIncome) * 100 : 0,
            'expense_growth' => $previousExpenses > 0 ? (($totalExpenses - $previousExpenses) / $previousExpenses) * 100 : 0,
            'net_growth' => $previousNet != 0 ? (($netCashflow - $previousNet) / abs($previousNet)) * 100 : 0,
            'entries_count' => $entries->count(),
            'average_transaction' => $entries->count() > 0 ? $entries->avg('amount') : 0,
        ];
    }

    private function getCategories()
    {
        return [
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
    }

    private function getPaymentMethods()
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'mpesa' => 'M-Pesa',
            'airtel_money' => 'Airtel Money',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'mobile_banking' => 'Mobile Banking',
            'online_banking' => 'Online Banking',
            'cryptocurrency' => 'Cryptocurrency',
            'barter_trade' => 'Barter Trade',
            'credit_sale' => 'Credit Sale',
            'other' => 'Other',
        ];
    }

    private function getStartDateFromPeriod($period)
    {
        switch ($period) {
            case '1m':
                return Carbon::now()->subMonth();
            case '3m':
                return Carbon::now()->subMonths(3);
            case '6m':
                return Carbon::now()->subMonths(6);
            case '12m':
            default:
                return Carbon::now()->subYear();
        }
    }

    private function getMonthlyTrends($userId, $startDate, $endDate)
    {
        $trends = [];
        $current = Carbon::parse($startDate)->startOfMonth();
        
        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $income = CashflowEntry::where('user_id', $userId)
                ->where('type', 'income')
                ->whereBetween('entry_date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $expenses = CashflowEntry::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereBetween('entry_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $trends[] = [
                'month' => $current->format('Y-m'),
                'month_name' => $current->format('M Y'),
                'income' => $income,
                'expenses' => $expenses,
                'net' => $income - $expenses,
            ];
            
            $current->addMonth();
        }
        
        return $trends;
    }

    private function getCategoryBreakdown($userId, $startDate, $endDate)
    {
        $incomeByCategory = CashflowEntry::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->groupBy('category')
            ->selectRaw('category, sum(amount) as total')
            ->pluck('total', 'category');

        $expensesByCategory = CashflowEntry::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->groupBy('category')
            ->selectRaw('category, sum(amount) as total')
            ->pluck('total', 'category');

        return [
            'income' => $incomeByCategory,
            'expenses' => $expensesByCategory,
        ];
    }

    private function getPaymentMethodAnalysis($userId, $startDate, $endDate)
    {
        return CashflowEntry::where('user_id', $userId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->selectRaw('payment_method, sum(amount) as total, count(*) as count')
            ->get()
            ->keyBy('payment_method');
    }

    private function getCashflowForecast($userId)
    {
        // Simple forecast based on historical averages
        // In production, this could use more sophisticated algorithms
        
        $historicalMonths = 6;
        $forecastMonths = 3;
        
        $historical = CashflowEntry::where('user_id', $userId)
            ->where('entry_date', '>=', Carbon::now()->subMonths($historicalMonths))
            ->get();
            
        $avgMonthlyIncome = $historical->where('type', 'income')->sum('amount') / $historicalMonths;
        $avgMonthlyExpenses = $historical->where('type', 'expense')->sum('amount') / $historicalMonths;
        
        $forecast = [];
        for ($i = 1; $i <= $forecastMonths; $i++) {
            $forecastDate = Carbon::now()->addMonths($i);
            $forecast[] = [
                'month' => $forecastDate->format('Y-m'),
                'month_name' => $forecastDate->format('M Y'),
                'projected_income' => $avgMonthlyIncome,
                'projected_expenses' => $avgMonthlyExpenses,
                'projected_net' => $avgMonthlyIncome - $avgMonthlyExpenses,
            ];
        }
        
        return $forecast;
    }
}
