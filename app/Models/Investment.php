<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'details_of_investment',
        'description',
        'initial_amount',
        'current_amount',
        'target_amount',
        'start_date',
        'target_date',
        'expected_return_rate',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
        'initial_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'expected_return_rate' => 'decimal:4',
    ];

    // An investment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An investment has many contributions
    public function contributions()
    {
        return $this->hasMany(InvestmentContribution::class);
    }
}
