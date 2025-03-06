<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Instead of using a Category model, define static arrays for categories.
        $incomeCategories = collect([
            ['name' => 'Salary'],
        ]);

        $expenseCategories = collect([
            ['name' => 'Housing'],
            ['name' => 'Transportation'],
            ['name' => 'Food'],
            ['name' => 'Utilities'],
            ['name' => 'Entertainment'],
            ['name' => 'Other'],
        ]);

        foreach ($users as $user) {
            // Create income and expense transactions for the past 3 months
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::now()->subMonths($i);

                // Create monthly salary income
                $incomeCategory = $incomeCategories->first();
                $incomeTransaction = Transaction::create([
                    'user_id'          => $user->id,
                    'type'             => 'income',
                    'category'         => $incomeCategory['name'],
                    'amount'           => rand(3000, 5000),
                    'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(1, 5)),
                    'description'      => 'Monthly salary',
                ]);

                Income::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $incomeTransaction->id, // Storing the transaction foreign key
                    'type'           => 'Salary',
                    'category'       => 'Salary',
                    'amount'         => $incomeTransaction->amount,
                    'description'    => 'Monthly salary',
                    'income_date'    => $incomeTransaction->transaction_date,
                ]);

                // Create an additional income transaction randomly
                if (rand(0, 1)) {
                    $incomeTransaction = Transaction::create([
                        'user_id'          => $user->id,
                        'type'             => 'income',
                        'category'         => 'Salary',
                        'amount'           => rand(500, 1500),
                        'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(10, 20)),
                        'description'      => 'Additional salary payment',
                    ]);

                    Income::create([
                        'user_id'        => $user->id,
                        'transaction_id' => $incomeTransaction->id, // Storing the transaction foreign key
                        'type'           => 'Salary',
                        'category'       => 'Salary',
                        'amount'         => $incomeTransaction->amount,
                        'description'    => 'Additional salary payment',
                        'income_date'    => $incomeTransaction->transaction_date,
                    ]);
                }

                // Create multiple expenses for each month
                $numberOfExpenses = rand(8, 15);
                for ($j = 0; $j < $numberOfExpenses; $j++) {
                    $expenseCategory = $expenseCategories->random();
                    $amount = 0;

                    // Set realistic amounts based on category
                    switch ($expenseCategory['name']) {
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
                        'user_id'          => $user->id,
                        'type'             => 'expense',
                        'category'         => $expenseCategory['name'],
                        'amount'           => $amount,
                        'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(1, 28)),
                        'description'      => $expenseCategory['name'] . ' expense',
                    ]);

                    Expense::create([
                        'user_id'        => $user->id,
                        'transaction_id' => $expenseTransaction->id, // Storing the transaction foreign key
                        'type'           => 'expense',
                        'category'       => $expenseCategory['name'],
                        'amount'         => $expenseTransaction->amount,
                        'description'    => $expenseCategory['name'] . ' expense',
                        'expense_date'   => $expenseTransaction->transaction_date,
                    ]);
                }
            }
        }
    }
}
