<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenseCategories = [
            ['name' => 'Groceries', 'description' => 'Apples', 'type' => 'expense'],
            ['name' => 'Transport', 'description' => 'Public transport', 'type' => 'expense'],
            ['name' => 'Utilities', 'description' => 'electricity', 'type' => 'expense'],
            ['name' => 'Entertainment', 'description' => 'eat out with friends', 'type' => 'expense'],
            ['name' => 'Healthcare', 'description' => 'bristol park clinic.', 'type' => 'expense'],
            ['name' => 'Insurance', 'description' => 'insurance cover for car', 'type' => 'expense'],
            ['name' => 'Investments', 'description' => 'Sacco', 'type' => 'expense'],
            ['name' => 'Goal Contribution', 'description' => 'Buy Bike', 'type' => 'expense'],
            ['name' => 'Debt Payments', 'description' => 'School Fees', 'type' => 'expense'],
            ['name' => 'Rent', 'description' => 'House Rent', 'type' => 'expense'],
            ['name' => 'Gifts/Donations', 'description' => 'Tithe at church', 'type' => 'expense'],
            ['name' => 'Other', 'description' => 'Other Expense', 'type' => 'expense'],
        ];

        // Create default income categories
        $incomeCategories = [
            ['name' => 'Salary', 'description' => 'Regular employment income', 'type' => 'income'],
            ['name' => 'Freelance', 'description' => 'Contract or freelance work', 'type' => 'income'],
            ['name' => 'Business', 'description' => 'Business income', 'type' => 'income'],
            ['name' => 'Investments', 'description' => 'Dividends, interest, capital gains', 'type' => 'income'],
            ['name' => 'Gifts', 'description' => 'Money received as gifts', 'type' => 'income'],
            ['name' => 'Rental Income', 'description' => 'Income from rental properties', 'type' => 'income'],
            ['name' => 'Pension Income', 'description' => 'Income from pension', 'type' => 'income'],
            ['name' => 'Other', 'description' => 'Other sources of income', 'type' => 'income'],
        ];

        // Insert all categories
        foreach ($expenseCategories as $category) {
            Category::create($category);
        }

        foreach ($incomeCategories as $category) {
            Category::create($category);
        }

        // Create some user-specific categories
        $users = User::all();
        foreach ($users as $user) {
            Category::create([
                'name' => 'Custom Expense',
                'description' => 'User-defined expense category',
                'type' => 'expense',
                'user_id' => $user->id,
            ]);

            Category::create([
                'name' => 'Custom Income',
                'description' => 'User-defined income category',
                'type' => 'income',
                'user_id' => $user->id,
            ]);
        }
    }
}
