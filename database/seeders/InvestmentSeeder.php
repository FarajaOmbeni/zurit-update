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
            ['name' => 'Cytonn Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 12.0],
            ['name' => 'Sanlam Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 11.5],
            ['name' => 'Zimele Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 10.5],
            ['name' => 'Britam Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 10.2],
            ['name' => 'Madison Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 10.0],
            ['name' => 'NCBA Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 9.8],
            ['name' => 'Nabo Capital Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 9.5],
            ['name' => 'ICEA Lion Money Market Fund', 'type' => 'Money Market Fund', 'returnRate' => 9.2],
            ['name' => 'Infrastructure Bond', 'type' => 'Bonds', 'returnRate' => 14.0],
            ['name' => 'Government Bond', 'type' => 'Bonds', 'returnRate' => 13.5],
            ['name' => 'Corporate Bond', 'type' => 'Bonds', 'returnRate' => 12.0],
            ['name' => '91-Day Treasury Bill', 'type' => 'Treasury Bills', 'returnRate' => 12.8],
            ['name' => '182-Day Treasury Bill', 'type' => 'Treasury Bills', 'returnRate' => 13.2],
            ['name' => '364-Day Treasury Bill', 'type' => 'Treasury Bills', 'returnRate' => 13.5],
        ];

        foreach ($users as $user) {
            $numberOfInvestments = rand(2, 5);
            $shuffledInvestments = collect($investmentTypes)->shuffle();

            for ($i = 0; $i < $numberOfInvestments; $i++) {
                if ($i >= count($investmentTypes)) break;

                $investmentType = $shuffledInvestments[$i];
                $initialAmount = rand(1000, 10000);
                $startDate = Carbon::now()->subMonths(rand(3, 18));
                $targetDate = rand(0, 1) ? Carbon::now()->addYears(rand(2, 10)) : null;

                $returnRate = $investmentType['returnRate'];
                $monthsActive = Carbon::now()->diffInMonths($startDate);
                $yearFraction = $monthsActive / 12;
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
