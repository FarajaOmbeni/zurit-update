<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPayment extends Model
{
    use HasFactory;
    protected $table = 'monthly_payments';
    protected $fillable = [
        'debt_name',
        'amount',
        'payment_date',
    ];
}
