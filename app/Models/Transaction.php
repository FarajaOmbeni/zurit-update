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
        'next_run_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // A transaction belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A transaction can be a goal contribution
    public function goalContribution()
    {
        return $this->hasOne(GoalContribution::class);
    }

    // A transaction can be an investment contribution
    public function investmentContribution()
    {
        return $this->hasOne(InvestmentContribution::class);
    }

    // A transaction can be an expense
    public function debtPayment()
    {
        return $this->hasOne(DebtPayment::class);
    }

    public function income() 
    {
        return $this->hasOne(Income::class);
    }

    public function expense()
    {
        return $this->hasOne(Expense::class);
    }
}
