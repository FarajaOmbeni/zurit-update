<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo user with a phone number as required by your ERD.
        User::create([
            'name'              => 'johndoe',
            'email'             => 'john@example.com',
            'phone_number'      => '123-456-7890',
            'password'          => Hash::make('password'),
            'role'              => 'user',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
