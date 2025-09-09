<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BalanceSheetRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'as_of_date',
        // Current Assets
        'cash_and_equivalents',
        'accounts_receivable',
        'inventory',
        'prepaid_expenses',
        'other_current_assets',
        // Non-Current Assets
        'property_plant_equipment',
        'accumulated_depreciation',
        'intangible_assets',
        'investments',
        'other_non_current_assets',
        // Current Liabilities
        'accounts_payable',
        'short_term_debt',
        'accrued_liabilities',
        'taxes_payable',
        'other_current_liabilities',
        // Non-Current Liabilities
        'long_term_debt',
        'deferred_tax_liabilities',
        'other_non_current_liabilities',
        // Equity
        'share_capital',
        'retained_earnings',
        'other_equity',
        'currency',
        'is_automated',
        'notes',
    ];

    protected $casts = [
        'as_of_date' => 'date',
        'cash_and_equivalents' => 'decimal:2',
        'accounts_receivable' => 'decimal:2',
        'inventory' => 'decimal:2',
        'prepaid_expenses' => 'decimal:2',
        'other_current_assets' => 'decimal:2',
        'property_plant_equipment' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'intangible_assets' => 'decimal:2',
        'investments' => 'decimal:2',
        'other_non_current_assets' => 'decimal:2',
        'accounts_payable' => 'decimal:2',
        'short_term_debt' => 'decimal:2',
        'accrued_liabilities' => 'decimal:2',
        'taxes_payable' => 'decimal:2',
        'other_current_liabilities' => 'decimal:2',
        'long_term_debt' => 'decimal:2',
        'deferred_tax_liabilities' => 'decimal:2',
        'other_non_current_liabilities' => 'decimal:2',
        'share_capital' => 'decimal:2',
        'retained_earnings' => 'decimal:2',
        'other_equity' => 'decimal:2',
        'is_automated' => 'boolean',
    ];

    // Belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate total current assets
    public function getTotalCurrentAssetsAttribute()
    {
        return $this->cash_and_equivalents + $this->accounts_receivable + 
               $this->inventory + $this->prepaid_expenses + $this->other_current_assets;
    }

    // Calculate total non-current assets
    public function getTotalNonCurrentAssetsAttribute()
    {
        return $this->property_plant_equipment - $this->accumulated_depreciation + 
               $this->intangible_assets + $this->investments + $this->other_non_current_assets;
    }

    // Calculate total assets
    public function getTotalAssetsAttribute()
    {
        return $this->total_current_assets + $this->total_non_current_assets;
    }

    // Calculate total current liabilities
    public function getTotalCurrentLiabilitiesAttribute()
    {
        return $this->accounts_payable + $this->short_term_debt + 
               $this->accrued_liabilities + $this->taxes_payable + $this->other_current_liabilities;
    }

    // Calculate total non-current liabilities
    public function getTotalNonCurrentLiabilitiesAttribute()
    {
        return $this->long_term_debt + $this->deferred_tax_liabilities + $this->other_non_current_liabilities;
    }

    // Calculate total liabilities
    public function getTotalLiabilitiesAttribute()
    {
        return $this->total_current_liabilities + $this->total_non_current_liabilities;
    }

    // Calculate total equity
    public function getTotalEquityAttribute()
    {
        return $this->share_capital + $this->retained_earnings + $this->other_equity;
    }

    // Check if balance sheet is balanced
    public function getIsBalancedAttribute()
    {
        return abs($this->total_assets - ($this->total_liabilities + $this->total_equity)) < 0.01;
    }

    // Calculate current ratio
    public function getCurrentRatioAttribute()
    {
        return $this->total_current_liabilities > 0 ? $this->total_current_assets / $this->total_current_liabilities : 0;
    }

    // Calculate debt to equity ratio
    public function getDebtToEquityRatioAttribute()
    {
        return $this->total_equity > 0 ? $this->total_liabilities / $this->total_equity : 0;
    }
} 