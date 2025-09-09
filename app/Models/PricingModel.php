<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_service_name',
        'product_type', // 'product' or 'service'
        'industry_template', // 'manufacturing', 'retail', 'service', 'agriculture', etc.
        'raw_material_cost',
        'direct_labor_cost',
        'variable_overhead_cost',
        'fixed_overhead_cost',
        'total_cost_per_unit',
        'desired_profit_margin', // percentage
        'markup_percentage',
        'suggested_selling_price',
        'break_even_quantity',
        'break_even_revenue',
        'competitor_price_low',
        'competitor_price_high',
        'market_positioning', // 'premium', 'competitive', 'budget'
        'units_per_period',
        'period_type', // 'daily', 'weekly', 'monthly', 'yearly'
        'seasonal_adjustment',
        'currency',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'raw_material_cost' => 'decimal:2',
        'direct_labor_cost' => 'decimal:2',
        'variable_overhead_cost' => 'decimal:2',
        'fixed_overhead_cost' => 'decimal:2',
        'total_cost_per_unit' => 'decimal:2',
        'desired_profit_margin' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'suggested_selling_price' => 'decimal:2',
        'break_even_quantity' => 'decimal:2',
        'break_even_revenue' => 'decimal:2',
        'competitor_price_low' => 'decimal:2',
        'competitor_price_high' => 'decimal:2',
        'units_per_period' => 'integer',
        'seasonal_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Auto-calculate pricing when model is saved
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->calculatePricing();
        });
    }

    // Calculate all pricing metrics
    public function calculatePricing()
    {
        // Calculate total cost per unit
        $this->total_cost_per_unit = $this->raw_material_cost + 
                                   $this->direct_labor_cost + 
                                   $this->variable_overhead_cost + 
                                   $this->fixed_overhead_cost;

        // Calculate suggested selling price based on desired margin
        if ($this->desired_profit_margin > 0) {
            $this->suggested_selling_price = $this->total_cost_per_unit / (1 - ($this->desired_profit_margin / 100));
        }

        // Calculate markup percentage
        if ($this->total_cost_per_unit > 0) {
            $this->markup_percentage = (($this->suggested_selling_price - $this->total_cost_per_unit) / $this->total_cost_per_unit) * 100;
        }

        // Calculate break-even metrics
        $this->calculateBreakEven();
    }

    // Calculate break-even analysis
    public function calculateBreakEven()
    {
        $variableCostPerUnit = $this->raw_material_cost + $this->direct_labor_cost + $this->variable_overhead_cost;
        $contributionMargin = $this->suggested_selling_price - $variableCostPerUnit;

        if ($contributionMargin > 0) {
            $this->break_even_quantity = $this->fixed_overhead_cost / $contributionMargin;
            $this->break_even_revenue = $this->break_even_quantity * $this->suggested_selling_price;
        }
    }

    // Sensitivity analysis - price impact
    public function sensitivityAnalysis($priceChanges = [-10, -5, 0, 5, 10])
    {
        $analysis = [];
        
        foreach ($priceChanges as $change) {
            $newPrice = $this->suggested_selling_price * (1 + $change / 100);
            $variableCostPerUnit = $this->raw_material_cost + $this->direct_labor_cost + $this->variable_overhead_cost;
            $contributionMargin = $newPrice - $variableCostPerUnit;
            $newBreakEven = $contributionMargin > 0 ? $this->fixed_overhead_cost / $contributionMargin : 0;
            
            $analysis[] = [
                'price_change_percent' => $change,
                'new_price' => $newPrice,
                'contribution_margin' => $contributionMargin,
                'break_even_quantity' => $newBreakEven,
                'profit_margin_percent' => $newPrice > 0 ? (($newPrice - $this->total_cost_per_unit) / $newPrice) * 100 : 0,
            ];
        }

        return $analysis;
    }

    // Get competitive position
    public function getCompetitivePositionAttribute()
    {
        if ($this->competitor_price_low && $this->competitor_price_high) {
            $avgCompetitorPrice = ($this->competitor_price_low + $this->competitor_price_high) / 2;
            
            if ($this->suggested_selling_price < $this->competitor_price_low) {
                return 'below_market';
            } elseif ($this->suggested_selling_price > $this->competitor_price_high) {
                return 'above_market';
            } else {
                return 'competitive';
            }
        }
        
        return 'unknown';
    }

    // Calculate projected monthly profit
    public function getProjectedMonthlyProfitAttribute()
    {
        $unitsPerMonth = $this->getUnitsPerMonth();
        $profitPerUnit = $this->suggested_selling_price - $this->total_cost_per_unit;
        
        return $unitsPerMonth * $profitPerUnit;
    }

    // Convert units to monthly basis
    private function getUnitsPerMonth()
    {
        $multipliers = [
            'daily' => 30,
            'weekly' => 4.33,
            'monthly' => 1,
            'yearly' => 1/12,
        ];

        return $this->units_per_period * ($multipliers[$this->period_type] ?? 1);
    }
} 