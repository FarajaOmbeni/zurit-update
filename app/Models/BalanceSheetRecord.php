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

    // Auto-generate from assets and liabilities
    public static function generateFromAssetsAndLiabilities($userId, $asOfDate)
    {
        // Get all assets for the user
        $assets = Asset::where('user_id', $userId)
            ->where('date_acquired', '<=', $asOfDate)
            ->get();

        // Get all liabilities for the user
        $liabilities = Liability::where('user_id', $userId)
            ->where('date_acquired', '<=', $asOfDate)
            ->get();

        // Calculate current assets
        $cashAndEquivalents = $assets->where('asset_type', 'cash')->sum('current_value');
        $accountsReceivable = $assets->where('asset_type', 'accounts_receivable')->sum('current_value');
        $inventory = $assets->where('asset_type', 'inventory')->sum('current_value');
        $prepaidExpenses = $assets->where('asset_type', 'prepaid_expenses')->sum('current_value');
        $otherCurrentAssets = $assets->where('asset_type', 'other_current')->sum('current_value');

        // Calculate non-current assets
        $propertyPlantEquipment = $assets->where('asset_type', 'property_plant_equipment')->sum('current_value');
        $accumulatedDepreciation = $assets->where('asset_type', 'property_plant_equipment')->sum('depreciation');
        $intangibleAssets = $assets->where('asset_type', 'intangible')->sum('current_value');
        $investments = $assets->where('asset_type', 'investment')->sum('current_value');
        $otherNonCurrentAssets = $assets->where('asset_type', 'other_non_current')->sum('current_value');

        // Calculate current liabilities
        $accountsPayable = $liabilities->where('liability_type', 'accounts_payable')->sum('current_balance');
        $shortTermDebt = $liabilities->where('liability_type', 'short_term_debt')->sum('current_balance');
        $accruedLiabilities = $liabilities->where('liability_type', 'accrued_liabilities')->sum('current_balance');
        $taxesPayable = $liabilities->where('liability_type', 'taxes_payable')->sum('current_balance');
        $otherCurrentLiabilities = $liabilities->where('liability_type', 'other_current')->sum('current_balance');

        // Calculate non-current liabilities
        $longTermDebt = $liabilities->where('liability_type', 'long_term_debt')->sum('current_balance');
        $deferredTaxLiabilities = $liabilities->where('liability_type', 'deferred_tax')->sum('current_balance');
        $otherNonCurrentLiabilities = $liabilities->where('liability_type', 'other_non_current')->sum('current_balance');

        // Calculate equity (simplified - would need more sophisticated calculation)
        $totalAssets = $cashAndEquivalents + $accountsReceivable + $inventory + $prepaidExpenses + 
                      $otherCurrentAssets + $propertyPlantEquipment - $accumulatedDepreciation + 
                      $intangibleAssets + $investments + $otherNonCurrentAssets;

        $totalLiabilities = $accountsPayable + $shortTermDebt + $accruedLiabilities + 
                           $taxesPayable + $otherCurrentLiabilities + $longTermDebt + 
                           $deferredTaxLiabilities + $otherNonCurrentLiabilities;

        $retainedEarnings = $totalAssets - $totalLiabilities; // Simplified calculation

        return self::create([
            'user_id' => $userId,
            'as_of_date' => $asOfDate,
            'cash_and_equivalents' => $cashAndEquivalents,
            'accounts_receivable' => $accountsReceivable,
            'inventory' => $inventory,
            'prepaid_expenses' => $prepaidExpenses,
            'other_current_assets' => $otherCurrentAssets,
            'property_plant_equipment' => $propertyPlantEquipment,
            'accumulated_depreciation' => $accumulatedDepreciation,
            'intangible_assets' => $intangibleAssets,
            'investments' => $investments,
            'other_non_current_assets' => $otherNonCurrentAssets,
            'accounts_payable' => $accountsPayable,
            'short_term_debt' => $shortTermDebt,
            'accrued_liabilities' => $accruedLiabilities,
            'taxes_payable' => $taxesPayable,
            'other_current_liabilities' => $otherCurrentLiabilities,
            'long_term_debt' => $longTermDebt,
            'deferred_tax_liabilities' => $deferredTaxLiabilities,
            'other_non_current_liabilities' => $otherNonCurrentLiabilities,
            'share_capital' => 0, // Would need to be set manually or calculated differently
            'retained_earnings' => $retainedEarnings,
            'other_equity' => 0,
            'is_automated' => true,
        ]);
    }
} 