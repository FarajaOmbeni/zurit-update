<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $assetTypes = [
            ['name' => 'Primary Home', 'type' => 'property', 'min' => 200000, 'max' => 800000],
            ['name' => 'Vehicle', 'type' => 'vehicle', 'min' => 5000, 'max' => 60000],
            ['name' => 'Savings Account', 'type' => 'cash', 'min' => 1000, 'max' => 50000],
            ['name' => 'Checking Account', 'type' => 'cash', 'min' => 500, 'max' => 10000],
            ['name' => 'Retirement Account', 'type' => 'investment', 'min' => 10000, 'max' => 200000],
            ['name' => 'Collectibles', 'type' => 'other', 'min' => 1000, 'max' => 20000],
            ['name' => 'Electronics', 'type' => 'other', 'min' => 500, 'max' => 10000],
            ['name' => 'Jewelry', 'type' => 'other', 'min' => 1000, 'max' => 30000],
        ];

        foreach ($users as $user) {
            // Create 3-6 assets for each user
            $numberOfAssets = rand(3, 6);
            $shuffledAssets = collect($assetTypes)->shuffle();

            for ($i = 0; $i < $numberOfAssets; $i++) {
                if ($i >= count($assetTypes)) break;

                $assetType = $shuffledAssets[$i];
                $value = rand($assetType['min'], $assetType['max']);
                $acquisitionDate = Carbon::now()->subMonths(rand(1, 120));

                Asset::create([
                    'user_id' => $user->id,
                    'name' => $assetType['name'],
                    'type' => $assetType['type'],
                    'description' => 'User\'s ' . strtolower($assetType['name']),
                    'value' => $value,
                    'acquisition_date' => $acquisitionDate,
                ]);
            }
        }
    }
}
