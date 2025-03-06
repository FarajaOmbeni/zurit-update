<?php

namespace Database\Seeders;

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
            // Removed CategorySeeder since your ERD no longer includes a categories table.
            TransactionSeeder::class, // This will seed incomes and expenses
            GoalSeeder::class,
            InvestmentSeeder::class,
            DebtSeeder::class,
            AssetSeeder::class,
            LiabilitySeeder::class,
        ]);
    }
}
