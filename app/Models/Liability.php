<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Liability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'liability_type', // For balance sheet categorization
        'description',
        'amount',
        'current_balance', // Current outstanding balance
        'due_date',
        'date_acquired', // When the liability was incurred
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_acquired' => 'date',
        'amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    // A liability belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
