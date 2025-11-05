<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthEndClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'opening_stock_value',
        'purchases_total',
        'closing_stock_value',
        'calculated_cogs',
        'depreciation_posted',
        'closed_at',
        'locked',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'opening_stock_value' => 'decimal:2',
        'purchases_total' => 'decimal:2',
        'closing_stock_value' => 'decimal:2',
        'calculated_cogs' => 'decimal:2',
        'depreciation_posted' => 'boolean',
        'locked' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

