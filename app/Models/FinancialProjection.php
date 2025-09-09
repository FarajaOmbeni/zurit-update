<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialProjection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_plan_id',
        'projection_name',
        'scenario_type', // 'optimistic', 'realistic', 'pessimistic', 'custom'
        'projection_period', // 'monthly', 'quarterly', 'yearly'
        'projection_years',
        'base_year_revenue',
        'base_year_expenses',
        'base_year_profit',
        // Growth Assumptions
        'revenue_growth_rate',
        'expense_growth_rate',
        'inflation_rate',
        'market_growth_rate',
        'customer_acquisition_rate',
        'customer_retention_rate',
        'average_order_value_growth',
        // Scenario Variables
        'best_case_revenue_multiplier',
        'worst_case_revenue_multiplier',
        'seasonal_adjustments',
        'market_penetration_rate',
        'pricing_changes',
        // Projected Data (JSON arrays for multi-year projections)
        'projected_revenue',
        'projected_cogs',
        'projected_gross_profit',
        'projected_operating_expenses',
        'projected_net_profit',
        'projected_cash_flow',
        'projected_assets',
        'projected_liabilities',
        'projected_equity',
        // Key Metrics
        'break_even_month',
        'payback_period',
        'roi_percentage',
        'irr_percentage',
        'npv_amount',
        // Assumptions and Notes
        'key_assumptions',
        'risk_factors',
        'sensitivity_analysis',
        'external_factors',
        'currency',
        'created_date',
        'last_updated',
        'is_baseline',
        'confidence_level',
    ];

    protected $casts = [
        'projected_revenue' => 'array',
        'projected_cogs' => 'array',
        'projected_gross_profit' => 'array',
        'projected_operating_expenses' => 'array',
        'projected_net_profit' => 'array',
        'projected_cash_flow' => 'array',
        'projected_assets' => 'array',
        'projected_liabilities' => 'array',
        'projected_equity' => 'array',
        'seasonal_adjustments' => 'array',
        'pricing_changes' => 'array',
        'key_assumptions' => 'array',
        'risk_factors' => 'array',
        'sensitivity_analysis' => 'array',
        'external_factors' => 'array',
        'base_year_revenue' => 'decimal:2',
        'base_year_expenses' => 'decimal:2',
        'base_year_profit' => 'decimal:2',
        'revenue_growth_rate' => 'decimal:4',
        'expense_growth_rate' => 'decimal:4',
        'inflation_rate' => 'decimal:4',
        'market_growth_rate' => 'decimal:4',
        'customer_acquisition_rate' => 'decimal:4',
        'customer_retention_rate' => 'decimal:4',
        'average_order_value_growth' => 'decimal:4',
        'best_case_revenue_multiplier' => 'decimal:2',
        'worst_case_revenue_multiplier' => 'decimal:2',
        'market_penetration_rate' => 'decimal:4',
        'roi_percentage' => 'decimal:2',
        'irr_percentage' => 'decimal:2',
        'npv_amount' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'projection_years' => 'integer',
        'break_even_month' => 'integer',
        'payback_period' => 'integer',
        'created_date' => 'date',
        'last_updated' => 'datetime',
        'is_baseline' => 'boolean',
    ];

    // Belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Belongs to a business plan (optional)
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    // Auto-calculate projections when model is saved
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty(['base_year_revenue', 'revenue_growth_rate', 'expense_growth_rate', 'projection_years'])) {
                $model->calculateProjections();
            }
        });
    }

    // Calculate all financial projections
    public function calculateProjections()
    {
        $years = $this->projection_years ?: 3;
        $revenueGrowth = $this->revenue_growth_rate ?: 0.1;
        $expenseGrowth = $this->expense_growth_rate ?: 0.05;
        
        $projectedRevenue = [];
        $projectedExpenses = [];
        $projectedProfit = [];
        $projectedCashFlow = [];

        $currentRevenue = $this->base_year_revenue ?: 0;
        $currentExpenses = $this->base_year_expenses ?: 0;

        for ($year = 1; $year <= $years; $year++) {
            // Apply growth rates
            $yearRevenue = $currentRevenue * pow(1 + $revenueGrowth, $year);
            $yearExpenses = $currentExpenses * pow(1 + $expenseGrowth, $year);
            
            // Apply scenario multipliers
            if ($this->scenario_type === 'optimistic' && $this->best_case_revenue_multiplier) {
                $yearRevenue *= $this->best_case_revenue_multiplier;
            } elseif ($this->scenario_type === 'pessimistic' && $this->worst_case_revenue_multiplier) {
                $yearRevenue *= $this->worst_case_revenue_multiplier;
            }

            // Apply seasonal adjustments if provided
            if ($this->seasonal_adjustments) {
                $yearRevenue = $this->applySeasonalAdjustments($yearRevenue, $year);
            }

            $yearProfit = $yearRevenue - $yearExpenses;
            $yearCashFlow = $this->calculateCashFlow($yearRevenue, $yearExpenses, $year);

            $projectedRevenue["year_{$year}"] = round($yearRevenue, 2);
            $projectedExpenses["year_{$year}"] = round($yearExpenses, 2);
            $projectedProfit["year_{$year}"] = round($yearProfit, 2);
            $projectedCashFlow["year_{$year}"] = round($yearCashFlow, 2);
        }

        $this->projected_revenue = $projectedRevenue;
        $this->projected_operating_expenses = $projectedExpenses;
        $this->projected_net_profit = $projectedProfit;
        $this->projected_cash_flow = $projectedCashFlow;

        // Calculate break-even analysis
        $this->calculateBreakEven();
        
        // Calculate ROI and other metrics
        $this->calculateFinancialMetrics();
    }

    private function applySeasonalAdjustments($revenue, $year)
    {
        $adjustments = $this->seasonal_adjustments ?: [];
        $totalAdjustment = 1.0;

        foreach ($adjustments as $adjustment) {
            if (isset($adjustment['year']) && $adjustment['year'] == $year) {
                $totalAdjustment *= (1 + ($adjustment['percentage'] / 100));
            }
        }

        return $revenue * $totalAdjustment;
    }

    private function calculateCashFlow($revenue, $expenses, $year)
    {
        // Simplified cash flow calculation
        // In practice, this would include working capital changes, capex, etc.
        $netIncome = $revenue - $expenses;
        
        // Add back depreciation (estimated at 5% of revenue)
        $depreciation = $revenue * 0.05;
        
        // Subtract capital expenditures (estimated at 3% of revenue for growth)
        $capex = $revenue * 0.03;
        
        // Working capital changes (estimated at 2% of revenue growth)
        $wcChange = ($revenue * 0.02);

        return $netIncome + $depreciation - $capex - $wcChange;
    }

    private function calculateBreakEven()
    {
        // Find the month when cumulative cash flow becomes positive
        $projectedCF = $this->projected_cash_flow ?: [];
        $cumulativeCF = 0;
        $breakEvenMonth = null;

        foreach ($projectedCF as $year => $annualCF) {
            $monthlyCF = $annualCF / 12;
            $yearNum = (int) str_replace('year_', '', $year);
            
            for ($month = 1; $month <= 12; $month++) {
                $cumulativeCF += $monthlyCF;
                $totalMonth = (($yearNum - 1) * 12) + $month;
                
                if ($cumulativeCF > 0 && !$breakEvenMonth) {
                    $breakEvenMonth = $totalMonth;
                    break;
                }
            }
            
            if ($breakEvenMonth) break;
        }

        $this->break_even_month = $breakEvenMonth;
    }

    private function calculateFinancialMetrics()
    {
        $projectedProfits = array_values($this->projected_net_profit ?: []);
        $projectedCashFlows = array_values($this->projected_cash_flow ?: []);

        if (count($projectedProfits) > 0) {
            // Calculate simple ROI (total profit / initial investment)
            $totalProfit = array_sum($projectedProfits);
            $initialInvestment = $this->base_year_expenses ?: 1;
            $this->roi_percentage = ($totalProfit / $initialInvestment) * 100;

            // Calculate simple payback period
            $cumulativeProfit = 0;
            foreach ($projectedProfits as $index => $profit) {
                $cumulativeProfit += $profit;
                if ($cumulativeProfit > $initialInvestment) {
                    $this->payback_period = $index + 1;
                    break;
                }
            }
        }

        // Calculate NPV (simplified, using 10% discount rate)
        $discountRate = 0.10;
        $npv = 0;
        foreach ($projectedCashFlows as $year => $cashFlow) {
            $yearIndex = $year + 1;
            $npv += $cashFlow / pow(1 + $discountRate, $yearIndex);
        }
        $this->npv_amount = $npv - ($this->base_year_expenses ?: 0);
    }

    // Generate scenario comparison
    public function generateScenarioComparison($scenarios = ['optimistic', 'realistic', 'pessimistic'])
    {
        $comparison = [];
        $baseProjection = $this->replicate();

        foreach ($scenarios as $scenario) {
            $projection = $baseProjection->replicate();
            $projection->scenario_type = $scenario;
            
            // Adjust multipliers based on scenario
            switch ($scenario) {
                case 'optimistic':
                    $projection->revenue_growth_rate = $this->revenue_growth_rate * 1.5;
                    $projection->best_case_revenue_multiplier = 1.2;
                    break;
                case 'pessimistic':
                    $projection->revenue_growth_rate = $this->revenue_growth_rate * 0.5;
                    $projection->worst_case_revenue_multiplier = 0.8;
                    break;
                default: // realistic
                    break;
            }

            $projection->calculateProjections();
            
            $comparison[$scenario] = [
                'projected_revenue' => $projection->projected_revenue,
                'projected_profit' => $projection->projected_net_profit,
                'roi_percentage' => $projection->roi_percentage,
                'break_even_month' => $projection->break_even_month,
                'npv_amount' => $projection->npv_amount,
            ];
        }

        return $comparison;
    }

    // Sensitivity analysis for key variables
    public function performSensitivityAnalysis()
    {
        $variables = [
            'revenue_growth_rate' => [-0.05, -0.02, 0, 0.02, 0.05],
            'expense_growth_rate' => [-0.02, -0.01, 0, 0.01, 0.02],
            'customer_acquisition_rate' => [-0.1, -0.05, 0, 0.05, 0.1],
        ];

        $analysis = [];
        $baseProjection = $this->replicate();

        foreach ($variables as $variable => $changes) {
            $variableAnalysis = [];
            
            foreach ($changes as $change) {
                $projection = $baseProjection->replicate();
                $projection->$variable = $this->$variable + $change;
                $projection->calculateProjections();

                $variableAnalysis[] = [
                    'change' => $change,
                    'revenue_impact' => array_sum($projection->projected_revenue ?: []),
                    'profit_impact' => array_sum($projection->projected_net_profit ?: []),
                    'roi_impact' => $projection->roi_percentage,
                ];
            }
            
            $analysis[$variable] = $variableAnalysis;
        }

        $this->sensitivity_analysis = $analysis;
        return $analysis;
    }
} 