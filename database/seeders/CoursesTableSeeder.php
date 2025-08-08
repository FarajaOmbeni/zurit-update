<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            [
                'title' => 'Personal Budgeting Basics',
                'description' => 'Learn how to create and maintain a personal budget to manage your finances effectively.',
                'thumbnail' => 'budgeting.jpg',
                'is_active' => true
            ],
            [
                'title' => 'Understanding Credit Scores',
                'description' => 'Discover how credit scores work and how to improve yours for better financial opportunities.',
                'thumbnail' => 'credit-score.jpg',
                'is_active' => true
            ],
            [
                'title' => 'Smart Saving Strategies',
                'description' => 'Explore different saving methods and how to make your money work harder for you.',
                'thumbnail' => 'saving.jpg',
                'is_active' => true
            ],
            [
                'title' => 'Introduction to Investing',
                'description' => 'Learn the fundamentals of investing and how to start growing your wealth.',
                'thumbnail' => 'investing.jpg',
                'is_active' => true
            ],
            [
                'title' => 'Debt Management 101',
                'description' => 'Understand different types of debt and strategies to manage and reduce what you owe.',
                'thumbnail' => 'debt.jpg',
                'is_active' => true
            ],
            [
                'title' => 'Tax Fundamentals',
                'description' => 'Get familiar with basic tax concepts and how to prepare for tax season.',
                'thumbnail' => 'taxes.jpg',
                'is_active' => true
            ]
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('Successfully added financial literacy courses!');
    }
}