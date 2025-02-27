<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TransactionSeeder::class, // This will seed incomes and expenses
            GoalSeeder::class,
            InvestmentSeeder::class,
            DebtSeeder::class,
            AssetSeeder::class,
            LiabilitySeeder::class,
        ]);
    }
}
