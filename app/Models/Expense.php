<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'user_id',
        'expense_type',
        'expected_expense',
        'actual_expense',
        'description',
        'year_month',
        'is_loan',
        'is_goal',
        'is_investment',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($expense) {
            // Delete the corresponding Debt instance
            Debt::where('user_id', $expense->user_id)
                ->where('current_balance', $expense->actual_expense)
                ->delete();
        });
    }
}