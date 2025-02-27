<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributions()
    {
        return $this->hasMany(InvestmentContribution::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    public function getReturnOnInvestmentAttribute()
    {
        $totalContributed = $this->initial_amount + $this->contributions()->sum('amount');

        if ($totalContributed <= 0) {
            return 0;
        }

        return (($this->current_amount - $totalContributed) / $totalContributed) * 100;
    }
}
