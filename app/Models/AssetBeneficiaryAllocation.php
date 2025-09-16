<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBeneficiaryAllocation extends Model
{
    protected $fillable = [
        'asset_id',
        'beneficiary_id',
        'percentage',
        'conditions',
        'contingent_of',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function contingentOf()
    {
        return $this->belongsTo(Beneficiary::class, 'contingent_of');
    }
}
