<?php

namespace App\Traits;

use App\Models\Income;
use App\Models\Expense;

trait NetIncomeCalculator
{
    public function calculateNetIncome($userId)
    {
        $actualIncome = Income::where('user_id', $userId)->sum('actual_income');
        $actualExpenses = Expense::where('user_id', $userId)->sum('actual_expense');
        return $actualIncome - $actualExpenses;
    }
}
?>