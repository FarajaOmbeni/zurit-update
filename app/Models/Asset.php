<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'asset_type', // For balance sheet categorization
        'description',
        'value',
        'current_value', // Current market value
        'acquisition_date',
        'date_acquired', // Alias for acquisition_date
        'depreciation', // Accumulated depreciation
        'is_depreciable',
        'useful_life_months',
        'residual_value',
        'depreciation_start',
        'is_legacy',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'date_acquired' => 'date',
        'value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'is_legacy' => 'boolean',
        'is_depreciable' => 'boolean',
    ];

    // An asset belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Legacy asset allocations to beneficiaries
    public function beneficiaryAllocations()
    {
        return $this->hasMany(AssetBeneficiaryAllocation::class);
    }

    // Scope for legacy assets only
    public function scopeLegacy($query)
    {
        return $query->where('is_legacy', true);
    }

    // Compute straight-line monthly depreciation amount (simple helper)
    public function getMonthlyDepreciationAmount(): float
    {
        if (!$this->is_depreciable || !$this->useful_life_months || $this->useful_life_months <= 0) {
            return 0.0;
        }
        $depreciableBase = max(0, (float)$this->value - (float)($this->residual_value ?? 0));
        return round($depreciableBase / (int)$this->useful_life_months, 2);
    }
}
