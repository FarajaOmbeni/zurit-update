<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\User;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $debtTypes = [
            // Added a "rate" range and using "name" to generate a "type" later.
            ['name' => 'Mortgage',       'rate' => [3, 5],    'min' => 100000, 'max' => 500000],
            ['name' => 'Car Loan',       'rate' => [4, 7],    'min' => 10000,  'max' => 50000],
            ['name' => 'Student Loan',   'rate' => [4, 7],    'min' => 10000,  'max' => 100000],
            ['name' => 'Credit Card',    'rate' => [15, 25],  'min' => 1000,   'max' => 15000],
            ['name' => 'Personal Loan',  'rate' => [7, 12],   'min' => 5000,   'max' => 30000],
            ['name' => 'Medical Debt',   'rate' => [0, 5],    'min' => 2000,   'max' => 20000],
        ];

        foreach ($users as $user) {
            // Create 2-4 debts for each user
            $numberOfDebts = rand(2, 4);
            $shuffledDebts = collect($debtTypes)->shuffle();

            for ($i = 0; $i < $numberOfDebts; $i++) {
                if ($i >= count($debtTypes)) break;

                $debtType = $shuffledDebts[$i];

                // Generate initial debt amount and dates
                $initialAmount = rand($debtType['min'], $debtType['max']);
                $startDate = Carbon::now()->subMonths(rand(3, 36));
                $dueDate = Carbon::now()->addMonths(rand(12, 120));

                // Calculate interest rate and minimum payment
                // Interest rate is a percentage (e.g., 3.5 means 3.5%)
                $interestRate = rand($debtType['rate'][0] * 100, $debtType['rate'][1] * 100) / 100;
                $monthsBetween = max(1, $startDate->diffInMonths($dueDate));
                // Minimum payment is a simplified calculation (casting to int for this field)
                $minimumPayment = (int) round($initialAmount * ($interestRate / 100) / $monthsBetween);

                // Calculate current_amount as a random percentage (between 50% and 95% of the initial amount remaining)
                $progressPercentage = rand(5, 50) / 100;
                $currentAmount = round($initialAmount * (1 - $progressPercentage), 2);

                // Use the debt name as the basis for both "name" and "type"
                // For "type", we create a slug-like version of the name.
                $debtName = $debtType['name'];
                $debtTypeSlug = strtolower(str_replace(' ', '_', $debtName));

                // Create the debt record with all new fields
                $debt = Debt::create([
                    'user_id'        => $user->id,
                    'name'           => $debtName,
                    'type'           => $debtTypeSlug,
                    'description'    => $debtName . ' from ' . ['Bank of America', 'Chase', 'Wells Fargo', 'Citibank', 'Local Credit Union'][rand(0, 4)],
                    'initial_amount' => $initialAmount,
                    'current_amount' => $currentAmount,
                    'minimum_payment' => $minimumPayment,
                    'interest_rate'  => $interestRate,
                    'start_date'     => $startDate,
                    'due_date'       => $dueDate,
                    'status'         => 'active',
                ]);

                // Create several payments for this debt
                $numberOfPayments = rand(3, 12);
                // Total amount paid so far is a fraction of the initial amount.
                $totalPaid = round($initialAmount * ($progressPercentage), 2);
                $remainingToPay = $totalPaid;

                for ($j = 0; $j < $numberOfPayments; $j++) {
                    // On the last iteration, pay the remaining amount exactly.
                    $paymentAmount = $j == $numberOfPayments - 1
                        ? $remainingToPay
                        : round(min($remainingToPay, $totalPaid / $numberOfPayments * rand(8, 12) / 10), 2);

                    $remainingToPay -= $paymentAmount;

                    if ($paymentAmount <= 0) continue;

                    $paymentDate = Carbon::now()->subDays(rand(1, 365));

                    $transaction = Transaction::create([
                        'user_id'          => $user->id,
                        'type'             => 'debt',
                        'category'         => $debtName,
                        'amount'           => $paymentAmount,
                        'transaction_date' => $paymentDate,
                        'description'      => 'Payment for ' . $debtName,
                    ]);

                    DebtPayment::create([
                        'debt_id'        => $debt->id,
                        'transaction_id' => $transaction->id,
                        'amount'         => $paymentAmount,
                        'payment_date'   => $paymentDate,
                    ]);
                }
            }
        }
    }
}
