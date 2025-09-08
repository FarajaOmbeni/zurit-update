<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'national_id',
        'relationship',
        'is_minor',
        'contact',
    ];

    protected $casts = [
        'is_minor' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allocations()
    {
        return $this->hasMany(AssetBeneficiaryAllocation::class);
    }
}
