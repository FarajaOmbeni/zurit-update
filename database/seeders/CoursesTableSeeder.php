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
                'slug' => 'personal-budgeting-basics'
            ],
            [
                'title' => 'Understanding Credit Scores',
                'slug' => 'understanding-credit-scores'
            ],
            [
                'title' => 'Smart Saving Strategies',
                'slug' => 'smart-saving-strategies'
            ],
            [
                'title' => 'Introduction to Investing',
                'slug' => 'introduction-to-investing'
            ],
            [
                'title' => 'Debt Management 101',
                'slug' => 'debt-management-101'
            ],
            [
                'title' => 'Tax Fundamentals',
                'slug' => 'tax-fundamentals'
            ]
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('Successfully added financial literacy courses!');
    }
}