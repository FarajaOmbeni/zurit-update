<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Static categories
        $incomeCategories = ['Salary'];
        $expenseCategories = [
            'Housing',
            'Transportation',
            'Food',
            'Groceries',
            'Utilities',
            'Entertainment',
            'Other'
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) { // Loop for past 3 months
                $date = Carbon::now()->subMonths($i);

                // Create main salary income
                $salaryAmount = rand(3000, 5000);
                $salaryDate = $date->copy()->startOfMonth()->addDays(rand(1, 5));

                $salaryTransaction = Transaction::create([
                    'user_id'          => $user->id,
                    'type'             => 'income',
                    'category'         => 'Salary',
                    'amount'           => $salaryAmount,
                    'transaction_date' => $salaryDate,
                    'description'      => 'Monthly salary',
                ]);

                Income::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $salaryTransaction->id,
                    'type'           => 'Salary',
                    'category'       => 'Salary',
                    'amount'         => $salaryAmount,
                    'description'    => 'Monthly salary',
                    'income_date'    => $salaryDate,
                ]);

                // Occasionally add a bonus salary
                if (rand(0, 1)) {
                    $bonusAmount = rand(500, 1500);
                    $bonusDate = $date->copy()->startOfMonth()->addDays(rand(10, 20));

                    $bonusTransaction = Transaction::create([
                        'user_id'          => $user->id,
                        'type'             => 'income',
                        'category'         => 'Salary',
                        'amount'           => $bonusAmount,
                        'transaction_date' => $bonusDate,
                        'description'      => 'Additional salary payment',
                    ]);

                    Income::create([
                        'user_id'        => $user->id,
                        'transaction_id' => $bonusTransaction->id,
                        'type'           => 'Salary',
                        'category'       => 'Salary',
                        'amount'         => $bonusAmount,
                        'description'    => 'Additional salary payment',
                        'income_date'    => $bonusDate,
                    ]);
                }

                // Generate expenses
                $numExpenses = rand(8, 15);
                for ($j = 0; $j < $numExpenses; $j++) {
                    $category = collect($expenseCategories)->random();
                    $expenseDate = $date->copy()->startOfMonth()->addDays(rand(1, 28));

                    $amount = match ($category) {
                        'Housing' => rand(800, 2000),
                        'Transportation' => rand(100, 500),
                        'Food' => rand(50, 300),
                        'Utilities' => rand(80, 250),
                        'Entertainment' => rand(20, 150),
                        default => rand(10, 200),
                    };

                    $isRecurring = Arr::random(['yes', 'no']);
                    $expenseTransaction = Transaction::create([
                        'user_id'          => $user->id,
                        'type'             => 'expense',
                        'category'         => $category,
                        'is_recurring'     => $isRecurring,
                        'recurrence_pattern' => $isRecurring === 'yes' ? Arr::random(['daily', 'weekly', 'monthly', 'quarterly', 'yearly']) : null,
                        'amount'           => $amount,
                        'transaction_date' => $expenseDate,
                        'description'      => "$category expense",
                    ]);

                    Expense::create([
                        'user_id'        => $user->id,
                        'transaction_id' => $expenseTransaction->id,
                        'type'           => 'expense',
                        'category'       => $category,
                        'amount'         => $amount,
                        'description'    => "$category expense",
                        'expense_date'   => $expenseDate,
                    ]);
                }
            }
        }
    }
}
