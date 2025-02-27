<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use App\Models\InvestmentContribution;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InvestmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $investmentTypes = [
            ['name' => 'S&P 500 Index Fund', 'type' => 'mutual_funds', 'returnRate' => [5, 12]],
            ['name' => 'Tech Growth ETF', 'type' => 'stocks', 'returnRate' => [8, 15]],
            ['name' => 'Bond Portfolio', 'type' => 'bonds', 'returnRate' => [2, 5]],
            ['name' => 'Real Estate Investment Trust', 'type' => 'real_estate', 'returnRate' => [6, 10]],
            ['name' => 'Dividend Stock Portfolio', 'type' => 'stocks', 'returnRate' => [4, 8]],
            ['name' => 'High-Yield Savings', 'type' => 'cash', 'returnRate' => [1, 3]],
            ['name' => 'Cryptocurrency', 'type' => 'alternative', 'returnRate' => [-10, 30]],
        ];

        foreach ($users as $user) {
            // Create 2-5 investments for each user
            $numberOfInvestments = rand(2, 5);
            $shuffledInvestments = collect($investmentTypes)->shuffle();

            for ($i = 0; $i < $numberOfInvestments; $i++) {
                if ($i >= count($investmentTypes)) break;

                $investmentType = $shuffledInvestments[$i];
                $initialAmount = rand(1000, 10000);
                $startDate = Carbon::now()->subMonths(rand(3, 18));
                $targetDate = rand(0, 1) ? Carbon::now()->addYears(rand(2, 10)) : null;

                // Calculate return rate and current value
                $returnRate = rand($investmentType['returnRate'][0] * 100, $investmentType['returnRate'][1] * 100) / 100;
                $monthsActive = Carbon::now()->diffInMonths($startDate);
                $yearFraction = $monthsActive / 12;

                // Simple compound interest calculation
                $appreciationFactor = pow(1 + ($returnRate / 100), $yearFraction);
                $currentAmount = round($initialAmount * $appreciationFactor, 2);

                $targetAmount = $targetDate ? round($initialAmount * pow(1 + ($returnRate / 100), Carbon::now()->diffInYears($targetDate) + $yearFraction), 2) : null;

                $investment = Investment::create([
                    'user_id' => $user->id,
                    'name' => $investmentType['name'],
                    'type' => $investmentType['type'],
                    'description' => 'Investment in ' . $investmentType['name'],
                    'initial_amount' => $initialAmount,
                    'current_amount' => $currentAmount,
                    'target_amount' => $targetAmount,
                    'start_date' => $startDate,
                    'target_date' => $targetDate,
                    'expected_return_rate' => $returnRate,
                    'status' => 'active',
                ]);

                // Create several contributions to this investment
                if (rand(0, 1)) {  // 50% chance of additional contributions
                    $numberOfContributions = rand(2, 6);

                    for ($j = 0; $j < $numberOfContributions; $j++) {
                        $contributionAmount = rand(500, 3000);
                        $contributionDate = $startDate->copy()->addDays(rand(1, (Carbon::now()->diffInDays($startDate))));

                        $transaction = Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'investment',
                            'amount' => $contributionAmount,
                            'transaction_date' => $contributionDate,
                            'description' => 'Contribution to ' . $investmentType['name'],
                            'status' => 'completed',
                        ]);

                        InvestmentContribution::create([
                            'investment_id' => $investment->id,
                            'transaction_id' => $transaction->id,
                            'amount' => $contributionAmount,
                            'contribution_date' => $contributionDate,
                            'notes' => rand(0, 1) ? 'Regular contribution' : 'Extra contribution',
                        ]);
                    }
                }
            }
        }
    }
}
