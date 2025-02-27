<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\User;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $debtTypes = [
            ['name' => 'Mortgage', 'type' => 'mortgage', 'rate' => [3, 5], 'min' => 100000, 'max' => 500000],
            ['name' => 'Car Loan', 'type' => 'auto_loan', 'rate' => [4, 7], 'min' => 10000, 'max' => 50000],
            ['name' => 'Student Loan', 'type' => 'student_loan', 'rate' => [4, 7], 'min' => 10000, 'max' => 100000],
            ['name' => 'Credit Card', 'type' => 'credit_card', 'rate' => [15, 25], 'min' => 1000, 'max' => 15000],
            ['name' => 'Personal Loan', 'type' => 'personal_loan', 'rate' => [7, 12], 'min' => 5000, 'max' => 30000],
            ['name' => 'Medical Debt', 'type' => 'medical', 'rate' => [0, 5], 'min' => 2000, 'max' => 20000],
        ];

        foreach ($users as $user) {
            // Create 2-4 debts for each user
            $numberOfDebts = rand(2, 4);
            $shuffledDebts = collect($debtTypes)->shuffle();

            for ($i = 0; $i < $numberOfDebts; $i++) {
                if ($i >= count($debtTypes)) break;

                $debtType = $shuffledDebts[$i];
                $initialAmount = rand($debtType['min'], $debtType['max']);
                $startDate = Carbon::now()->subMonths(rand(3, 36));
                $dueDate = Carbon::now()->addMonths(rand(12, 120));

                // Calculate a random progress percentage
                $progressPercentage = rand(5, 50) / 100;
                $currentAmount = round($initialAmount * (1 - $progressPercentage), 2);
                $interestRate = rand($debtType['rate'][0] * 100, $debtType['rate'][1] * 100) / 100;

                $debt = Debt::create([
                    'user_id' => $user->id,
                    'name' => $debtType['name'],
                    'type' => $debtType['type'],
                    'description' => $debtType['name'] . ' from ' . ['Bank of America', 'Chase', 'Wells Fargo', 'Citibank', 'Local Credit Union'][rand(0, 4)],
                    'initial_amount' => $initialAmount,
                    'current_amount' => $currentAmount,
                    'interest_rate' => $interestRate,
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                    'status' => 'active',
                ]);

                // Create several payments for this debt
                $numberOfPayments = rand(3, 12);
                $totalPaid = $initialAmount - $currentAmount;
                $remainingToPay = $totalPaid;

                for ($j = 0; $j < $numberOfPayments; $j++) {
                    $paymentAmount = $j == $numberOfPayments - 1 ?
                        $remainingToPay :
                        round(min($remainingToPay, $totalPaid / $numberOfPayments * rand(8, 12) / 10), 2);

                    $remainingToPay -= $paymentAmount;

                    if ($paymentAmount <= 0) continue;

                    // Split payment into principal and interest
                    $interestAmount = round($paymentAmount * $interestRate / 100, 2);
                    $principalAmount = $paymentAmount - $interestAmount;

                    $paymentDate = $startDate->copy()->addDays(rand(30, (Carbon::now()->diffInDays($startDate))));

                    $transaction = Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'debt',
                        'amount' => $paymentAmount,
                        'transaction_date' => $paymentDate,
                        'description' => 'Payment for ' . $debtType['name'],
                        'status' => 'completed',
                    ]);

                    DebtPayment::create([
                        'debt_id' => $debt->id,
                        'transaction_id' => $transaction->id,
                        'amount' => $paymentAmount,
                        'principal_amount' => $principalAmount,
                        'interest_amount' => $interestAmount,
                        'payment_date' => $paymentDate,
                        'notes' => rand(0, 1) ? 'Regular payment' : 'Extra payment',
                    ]);
                }
            }
        }
    }
}
