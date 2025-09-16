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
        'description',
        'value',
        'acquisition_date',
        'is_legacy',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'value' => 'decimal:2',
        'is_legacy' => 'boolean',
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
}
