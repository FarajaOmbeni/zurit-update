<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type', // 'manufacturing', 'retail', 'service', 'agriculture', etc.
        'industry_sector',
        'registration_number',
        'tax_pin',
        'vat_number',
        'business_address',
        'business_phone',
        'business_email',
        'website',
        'employees_count',
        'annual_revenue',
        'business_age_years',
        'primary_market', // 'local', 'regional', 'national', 'international'
        'target_customers',
        'main_products_services',
        'business_description',
        'operational_status', // 'active', 'seasonal', 'dormant'
        'fiscal_year_start',
        'fiscal_year_end',
        'currency', // 'KES', 'USD', etc.
        'timezone',
    ];

    protected $casts = [
        'fiscal_year_start' => 'date',
        'fiscal_year_end' => 'date',
        'annual_revenue' => 'decimal:2',
        'employees_count' => 'integer',
        'business_age_years' => 'integer',
    ];

    // Belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Business has many cashflow entries
    public function cashflowEntries()
    {
        return $this->hasMany(CashflowEntry::class, 'user_id', 'user_id');
    }

    // Business has many profit/loss records
    public function profitLossRecords()
    {
        return $this->hasMany(ProfitLossRecord::class, 'user_id', 'user_id');
    }

    // Business has many balance sheet records
    public function balanceSheetRecords()
    {
        return $this->hasMany(BalanceSheetRecord::class, 'user_id', 'user_id');
    }

    // Business has many pricing models
    public function pricingModels()
    {
        return $this->hasMany(PricingModel::class, 'user_id', 'user_id');
    }

    // Business has many business plans
    public function businessPlans()
    {
        return $this->hasMany(BusinessPlan::class, 'user_id', 'user_id');
    }

    // Business has many financial projections
    public function financialProjections()
    {
        return $this->hasMany(FinancialProjection::class, 'user_id', 'user_id');
    }

    // Get business size classification
    public function getBusinessSizeAttribute()
    {
        $revenue = $this->annual_revenue ?? 0;
        $employees = $this->employees_count ?? 0;

        if ($revenue <= 500000 || $employees <= 5) {
            return 'micro';
        } elseif ($revenue <= 5000000 || $employees <= 50) {
            return 'small';
        } elseif ($revenue <= 50000000 || $employees <= 100) {
            return 'medium';
        }
        
        return 'large';
    }
} 