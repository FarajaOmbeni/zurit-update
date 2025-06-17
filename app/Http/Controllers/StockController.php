<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('nse_stocks', now()->addMinutes(3), function () {
                $response = Http::timeout(30)
                    ->retry(3, 2000)
                    ->withHeaders([
                        'x-rapidapi-host' => 'nairobi-stock-exchange-nse.p.rapidapi.com',
                        'x-rapidapi-key' => config('services.rapidapi.key'),
                    ])->get('https://nairobi-stock-exchange-nse.p.rapidapi.com/stocks');

                if ($response->successful()) {
                    $data = $response->json();

                    // Store backup cache that lasts longer
                    Cache::put('nse_stocks_backup', $data, now()->addHours(24));

                    return $data;
                }

                throw new \Exception('API request failed: ' . $response->status());
            });

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('NSE Stock fetch error: ' . $e->getMessage());

            // Return stale data if available
            $backupData = Cache::get('nse_stocks_backup');
            if ($backupData) {
                return response()->json([
                    'data' => $backupData,
                    'stale' => true,
                    'message' => 'Showing cached data due to API issues'
                ]);
            }

            return response()->json([
                'error' => true,
                'message' => 'Stock data currently unavailable',
                'data' => []
            ], 503);
        }
    }
}
