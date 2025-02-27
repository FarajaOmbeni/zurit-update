<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $expenseCategories = Category::where('type', 'expense')->get();
        $incomeCategories = Category::where('type', 'income')->get();

        foreach ($users as $user) {
            // Create income transactions for the past 3 months
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::now()->subMonths($i);

                // Create monthly salary
                $incomeTransaction = Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'income',
                    'category_id' => $incomeCategories->where('name', 'Salary')->first()->id,
                    'amount' => rand(3000, 5000),
                    'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(1, 5)),
                    'description' => 'Monthly salary',
                    'status' => 'completed',
                ]);

                Income::create([
                    'user_id' => $user->id,
                    'category_id' => $incomeCategories->where('name', 'Salary')->first()->id,
                    'source' => 'Employer',
                    'amount' => $incomeTransaction->amount,
                    'frequency' => 'monthly',
                    'income_date' => $incomeTransaction->transaction_date,
                    'is_recurring' => true,
                ]);

                // Create an additional random income for some months
                if (rand(0, 1)) {
                    $incomeTransaction = Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'income',
                        'category_id' => $incomeCategories->where('name', 'Salary')->first()->id,
                        'amount' => rand(500, 1500),
                        'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(10, 20)),
                        'description' => 'Salary payment',
                        'status' => 'completed',
                    ]);

                    Income::create([
                        'user_id' => $user->id,
                        'category_id' => $incomeCategories->where('name', 'Salary')->first()->id,
                        'source' => 'Client ' . chr(rand(65, 90)),
                        'amount' => $incomeTransaction->amount,
                        'frequency' => 'one-time',
                        'income_date' => $incomeTransaction->transaction_date,
                        'is_recurring' => false,
                    ]);
                }

                // Create multiple expenses for each month
                $numberOfExpenses = rand(8, 15);
                for ($j = 0; $j < $numberOfExpenses; $j++) {
                    $expenseCategory = $expenseCategories->random();
                    $amount = 0;

                    // Set realistic amounts based on category
                    switch ($expenseCategory->name) {
                        case 'Housing':
                            $amount = rand(800, 2000);
                            break;
                        case 'Transportation':
                            $amount = rand(100, 500);
                            break;
                        case 'Food':
                            $amount = rand(50, 300);
                            break;
                        case 'Utilities':
                            $amount = rand(80, 250);
                            break;
                        case 'Entertainment':
                            $amount = rand(20, 150);
                            break;
                        default:
                            $amount = rand(10, 200);
                    }

                    $expenseTransaction = Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'expense',
                        'category_id' => $expenseCategory->id,
                        'amount' => $amount,
                        'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(1, 28)),
                        'description' => $expenseCategory->name . ' expense',
                        'status' => 'completed',
                    ]);

                    Expense::create([
                        'user_id' => $user->id,
                        'category_id' => $expenseCategory->id,
                        'description' => $expenseCategory->name . ' expense',
                        'amount' => $expenseTransaction->amount,
                        'frequency' => rand(0, 2) ? 'one-time' : 'monthly',
                        'expense_date' => $expenseTransaction->transaction_date,
                        'is_recurring' => rand(0, 2) ? false : true,
                    ]);
                }
            }
        }
    }
}
