<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitLossRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'revenue',
        'cost_of_goods_sold',
        'gross_profit',
        'operating_expenses',
        'other_income',
        'other_expenses',
        'ebitda',
        'depreciation',
        'amortization',
        'interest_expense',
        'interest_income',
        'tax_expense',
        'net_profit',
        'currency',
        'is_automated', // true if auto-calculated from cashflow
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'revenue' => 'decimal:2',
        'cost_of_goods_sold' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'operating_expenses' => 'decimal:2',
        'other_income' => 'decimal:2',
        'other_expenses' => 'decimal:2',
        'ebitda' => 'decimal:2',
        'depreciation' => 'decimal:2',
        'amortization' => 'decimal:2',
        'interest_expense' => 'decimal:2',
        'interest_income' => 'decimal:2',
        'tax_expense' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'is_automated' => 'boolean',
    ];

    // Belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate gross profit margin
    public function getGrossProfitMarginAttribute()
    {
        return $this->revenue > 0 ? ($this->gross_profit / $this->revenue) * 100 : 0;
    }

    // Calculate net profit margin
    public function getNetProfitMarginAttribute()
    {
        return $this->revenue > 0 ? ($this->net_profit / $this->revenue) * 100 : 0;
    }

    // Calculate operating margin
    public function getOperatingMarginAttribute()
    {
        $operating_profit = $this->gross_profit - $this->operating_expenses;
        return $this->revenue > 0 ? ($operating_profit / $this->revenue) * 100 : 0;
    }

    // Auto-calculate from cashflow entries
    public static function generateFromCashflow($userId, $startDate, $endDate)
    {
        // Check if a P&L record already exists for this period
        $existingRecord = self::where('user_id', $userId)
            ->where('period_start', $startDate)
            ->where('period_end', $endDate)
            ->first();

        if ($existingRecord) {
            return $existingRecord;
        }

        $incomeEntries = CashflowEntry::where('user_id', $userId)
            ->where('type', 'income')
            ->dateRange($startDate, $endDate)
            ->get();

        $expenseEntries = CashflowEntry::where('user_id', $userId)
            ->where('type', 'expense')
            ->dateRange($startDate, $endDate)
            ->get();

        $revenue = $incomeEntries->sum('amount');
        $totalExpenses = $expenseEntries->sum('amount');
        
        // Categorize expenses (this would be more sophisticated in practice)
        $costOfGoodsSold = $expenseEntries->whereIn('category', ['inventory', 'raw_materials', 'direct_labor'])->sum('amount');
        $operatingExpenses = $expenseEntries->whereIn('category', ['rent', 'utilities', 'salaries', 'marketing', 'administrative'])->sum('amount');
        $taxExpense = $expenseEntries->where('category', 'taxes')->sum('amount');
        $interestExpense = $expenseEntries->where('category', 'interest')->sum('amount');

        $grossProfit = $revenue - $costOfGoodsSold;
        $netProfit = $revenue - $totalExpenses;

        return self::create([
            'user_id' => $userId,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'revenue' => $revenue,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'tax_expense' => $taxExpense,
            'interest_expense' => $interestExpense,
            'net_profit' => $netProfit,
            'is_automated' => true,
        ]);
    }
} 