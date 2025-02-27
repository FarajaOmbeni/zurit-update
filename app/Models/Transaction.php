<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'category',
        'transaction_date',
        'description',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goalContribution()
    {
        return $this->hasOne(GoalContribution::class);
    }

    public function investmentContribution()
    {
        return $this->hasOne(InvestmentContribution::class);
    }

    public function debtPayment()
    {
        return $this->hasOne(DebtPayment::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
