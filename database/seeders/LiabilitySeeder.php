<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Liability;
use Illuminate\Database\Seeder;

class LiabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $liabilityTypes = [
            ['name' => 'Unpaid Bills', 'type' => 'other', 'min' => 100, 'max' => 1000],
            ['name' => 'Tax Liability', 'type' => 'tax', 'min' => 1000, 'max' => 10000],
            ['name' => 'Home Equity Line', 'type' => 'loan', 'min' => 5000, 'max' => 50000],
            ['name' => 'Business Loan', 'type' => 'loan', 'min' => 10000, 'max' => 100000],
        ];

        foreach ($users as $user) {
            // Create 0-3 liabilities for each user (apart from the debts already created)
            $numberOfLiabilities = rand(0, 3);
            $shuffledLiabilities = collect($liabilityTypes)->shuffle();

            for ($i = 0; $i < $numberOfLiabilities; $i++) {
                if ($i >= count($liabilityTypes)) break;

                $liabilityType = $shuffledLiabilities[$i];
                $amount = rand($liabilityType['min'], $liabilityType['max']);
                $dueDate = Carbon::now()->addDays(rand(15, 365));

                Liability::create([
                    'user_id'    => $user->id,
                    'name'       => $liabilityType['name'],
                    'category'   => $liabilityType['type'],  // Updated from 'type' to 'category'
                    'description' => 'User\'s ' . strtolower($liabilityType['name']),
                    'amount'     => $amount,
                    'due_date'   => $dueDate,
                ]);
            }
        }
    }
}
