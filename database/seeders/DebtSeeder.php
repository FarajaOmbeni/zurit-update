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
            ['name' => 'Credit Card', 'rate' => [15, 25], 'min' => 1000, 'max' => 15000],
            ['name' => 'Personal Loan', 'rate' => [7, 12], 'min' => 5000, 'max' => 30000],
            ['name' => 'Mortgage', 'rate' => [3, 5], 'min' => 100000, 'max' => 500000],
            ['name' => 'Auto Loan', 'rate' => [4, 7], 'min' => 10000, 'max' => 50000],
            ['name' => 'Student Loan', 'rate' => [4, 7], 'min' => 10000, 'max' => 100000],
            ['name' => 'Medical Debt', 'rate' => [0, 5], 'min' => 2000, 'max' => 20000],
            ['name' => 'Other', 'rate' => [5, 15], 'min' => 1000, 'max' => 50000],
        ];

        foreach ($users as $user) {
            $numberOfDebts = rand(2, 4);
            $shuffledDebts = collect($debtTypes)->shuffle();

            for ($i = 0; $i < $numberOfDebts; $i++) {
                if ($i >= count($debtTypes)) break;

                $debtType = $shuffledDebts[$i];

                $initialAmount = rand($debtType['min'], $debtType['max']);
                $startDate = Carbon::now()->subMonths(rand(3, 36));
                $dueDate = Carbon::now()->addMonths(rand(12, 120));

                $interestRate = rand($debtType['rate'][0] * 100, $debtType['rate'][1] * 100) / 100;
                $monthsBetween = max(1, $startDate->diffInMonths($dueDate));
                $minimumPayment = round(($initialAmount + $initialAmount * ($interestRate / 100)) / $monthsBetween);

                $progressPercentage = rand(5, 50) / 100;
                $currentAmount = round($initialAmount * (1 - $progressPercentage), 2);

                $debtName = $debtType['name'];
                $debtTypeSlug = strtolower(str_replace(' ', '_', $debtName));

                $debt = Debt::create([
                    'user_id' => $user->id,
                    'name' => $debtName,
                    'type' => $debtTypeSlug,
                    'description' => 'Debt Type: ' . $debtName,
                    'initial_amount' => $initialAmount,
                    'current_amount' => $currentAmount,
                    'minimum_payment' => $minimumPayment,
                    'interest_rate' => $interestRate,
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                    'status' => 'active',
                ]);

                $numberOfPayments = rand(3, 12);
                $totalPaid = round($initialAmount * ($progressPercentage), 2);
                $remainingToPay = $totalPaid;

                for ($j = 0; $j < $numberOfPayments; $j++) {
                    $paymentAmount = $j == $numberOfPayments - 1
                        ? $remainingToPay
                        : round(min($remainingToPay, $totalPaid / $numberOfPayments * rand(8, 12) / 10), 2);

                    $remainingToPay -= $paymentAmount;

                    if ($paymentAmount <= 0) continue;

                    $paymentDate = Carbon::now()->subDays(rand(1, 365));

                    $transaction = Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'debt',
                        'category' => $debtName,
                        'amount' => $paymentAmount,
                        'transaction_date' => $paymentDate,
                        'description' => 'Payment for ' . $debtName,
                    ]);

                    DebtPayment::create([
                        'debt_id' => $debt->id,
                        'transaction_id' => $transaction->id,
                        'amount' => $paymentAmount,
                        'payment_date' => $paymentDate,
                    ]);
                }
            }
        }
    }
}
