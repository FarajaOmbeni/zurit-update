<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;
    protected $table = 'debt_calc';
    protected $fillable = [
        'user_id',
        'debt_name',
        'current_balance',
        'interest_rate',
        'minimum_payment',
        'extra_payment',
        'start_period',
        'end_period',
        'payment_strategy',
        'debt_priority',
        'number_of_months',
        'minimum_monthly_payment',
        'category',
    ];

    const DEBT_TYPES = [
        'income_overdraft' => 'Income Overdraft',
        'loan' => 'Loan from Budget',
    ];
}
