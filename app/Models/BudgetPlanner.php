<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetPlanner extends Model
{
    use HasFactory;

    protected $table = 'budget_planner';

    protected $fillable = [
        'user_id',
        'income_type',
        'expected_income',
        'actual_income',
        'expense_type',
        'expected_expense',
        'actual_expense',
        'date',
    ];
    
}
