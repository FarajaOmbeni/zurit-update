<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Goal;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use App\Models\GoalContribution;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $goalTypes = [
            ['name' => 'Vacation', 'min' => 1000, 'max' => 5000],
            ['name' => 'Emergency Fund', 'min' => 5000, 'max' => 15000],
            ['name' => 'New Car', 'min' => 10000, 'max' => 30000],
            ['name' => 'Home Down Payment', 'min' => 20000, 'max' => 60000],
            ['name' => 'Wedding', 'min' => 15000, 'max' => 30000],
            ['name' => 'Education', 'min' => 5000, 'max' => 20000],
            ['name' => 'Home Renovation', 'min' => 5000, 'max' => 25000],
        ];

        foreach ($users as $user) {
            // Create 2-4 goals for each user
            $numberOfGoals = rand(2, 4);
            $shuffledGoals = collect($goalTypes)->shuffle();

            for ($i = 0; $i < $numberOfGoals; $i++) {
                if ($i >= count($goalTypes)) break;

                $goalType = $shuffledGoals[$i];
                $targetAmount = rand($goalType['min'], $goalType['max']);
                $startDate = Carbon::now()->subMonths(rand(1, 6));
                $targetDate = Carbon::now()->addMonths(rand(6, 24));

                // Calculate a random progress percentage
                $progressPercentage = rand(10, 90) / 100;
                $currentAmount = round($targetAmount * $progressPercentage, 2);

                $goal = Goal::create([
                    'user_id'        => $user->id,
                    'name'           => $goalType['name'],
                    'description'    => 'Saving for ' . strtolower($goalType['name']),
                    'target_amount'  => $targetAmount,
                    'current_amount' => $currentAmount,
                    'start_date'     => $startDate,
                    'target_date'    => $targetDate,
                    'status'         => 'in_progress',
                ]);

                // Create several contributions to this goal
                $numberOfContributions = rand(3, 8);
                $remainingAmount = $currentAmount;

                for ($j = 0; $j < $numberOfContributions; $j++) {
                    $contributionAmount = $j == $numberOfContributions - 1
                        ? $remainingAmount
                        : round(min($remainingAmount, $currentAmount / $numberOfContributions * rand(8, 12) / 10), 2);

                    $remainingAmount -= $contributionAmount;

                    if ($contributionAmount <= 0) continue;

                    $contributionDate = $startDate->copy()->addDays(rand(1, Carbon::now()->diffInDays($startDate)));

                    $transaction = Transaction::create([
                        'user_id'          => $user->id,
                        'type'             => 'goal',
                        'category'         => $goalType['name'],
                        'amount'           => $contributionAmount,
                        'transaction_date' => $contributionDate,
                        'description'      => 'Contribution to ' . $goalType['name'] . ' goal',
                    ]);

                    GoalContribution::create([
                        'goal_id'         => $goal->id,
                        'transaction_id'  => $transaction->id,
                        'amount'          => $contributionAmount,
                        'contribution_date' => $contributionDate,
                    ]);
                }
            }
        }
    }
}
