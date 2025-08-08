<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coach;

class CoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@zurit.com',
                'phone' => '+254 700 123 456',
                'bio' => 'Certified financial advisor with 8+ years of experience helping individuals achieve their financial goals. Specializes in debt management and investment planning.',
                'photo' => null,
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@zurit.com',
                'phone' => '+254 700 234 567',
                'bio' => 'Expert in personal finance and wealth building strategies. Passionate about teaching financial literacy and helping clients build sustainable wealth.',
                'photo' => null,
            ],
            [
                'name' => 'Grace Wanjiku',
                'email' => 'grace.wanjiku@zurit.com',
                'phone' => '+254 700 345 678',
                'bio' => 'Specialized in helping young professionals navigate their financial journey. Focuses on budgeting, saving, and early investment strategies.',
                'photo' => null,
            ],
            [
                'name' => 'David Ochieng',
                'email' => 'david.ochieng@zurit.com',
                'phone' => '+254 700 456 789',
                'bio' => 'Retirement planning specialist with expertise in pension management and long-term financial security. Helps clients prepare for their golden years.',
                'photo' => null,
            ],
            [
                'name' => 'Faith Muthoni',
                'email' => 'faith.muthoni@zurit.com',
                'phone' => '+254 700 567 890',
                'bio' => 'Business financial advisor helping entrepreneurs and small business owners optimize their finances and scale their operations effectively.',
                'photo' => null,
            ],
        ];

        foreach ($coaches as $coach) {
            Coach::create($coach);
        }
    }
}
