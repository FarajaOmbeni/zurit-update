<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use App\Models\InvestmentContribution;

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


                $investment = Investment::create([
                    'user_id'               => $user->id,
                    'type'                  => $investmentType['type'],
                    'details_of_investment' => $investmentType['name'],
                    'description'           => 'Investment in ' . $investmentType['name'],
                    'initial_amount'        => $initialAmount,
                    'current_amount'        => $currentAmount,
                    'start_date'            => $startDate,
                    'target_date'           => $targetDate,
                    'expected_return_rate'  => $returnRate,
                    'status'                => 'active',
                ]);

                // Create several contributions to this investment (50% chance)
                if (rand(0, 1)) {
                    $numberOfContributions = rand(2, 6);

                    for ($j = 0; $j < $numberOfContributions; $j++) {
                        $contributionAmount = rand(500, 3000);
                        $contributionDate = $startDate->copy()->addDays(rand(1, Carbon::now()->diffInDays($startDate)));

                        $transaction = Transaction::create([
                            'user_id'          => $user->id,
                            'type'             => 'investment',
                            'category'         => $investmentType['name'],
                            'amount'           => $contributionAmount,
                            'transaction_date' => $contributionDate,
                            'description'      => 'Contribution to ' . $investmentType['name'],
                        ]);

                        InvestmentContribution::create([
                            'investment_id'    => $investment->id,
                            'transaction_id'   => $transaction->id,
                            'amount'           => $contributionAmount,
                            'contribution_date' => $contributionDate,
                        ]);
                    }
                }
            }
        }
    }
}
