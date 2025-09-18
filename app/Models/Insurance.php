<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider_name',
        'coverage_amount',
        'premium_amount',
        'renewal_date',
        'notes',
        'policy_number',
    ];

    // A insurance belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A insurance has many beneficiaries
    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }
}
