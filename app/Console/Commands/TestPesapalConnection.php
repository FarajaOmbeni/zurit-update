<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PesapalService;
use App\Models\User;

class TestPesapalConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesapal:test {--user-id= : User ID to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Pesapal API connection and token retrieval';

    private $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        parent::__construct();
        $this->pesapalService = $pesapalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Pesapal API Connection...');
        
        // Test 1: Check configuration
        $this->info('1. Checking configuration...');
        $consumerKey = config('pesapal.PESAPAL_CONSUMER_KEY');
        $consumerSecret = config('pesapal.PESAPAL_CONSUMER_SECRET');
        $environment = config('pesapal.PESAPAL_ENVIRONMENT');
        
        if (!$consumerKey || !$consumerSecret) {
            $this->error('Missing Pesapal credentials in configuration!');
            $this->info('Please ensure these environment variables are set:');
            $this->info('PESAPAL_CONSUMER_KEY');
            $this->info('PESAPAL_CONSUMER_SECRET');
            $this->info('PESAPAL_ENVIRONMENT');
            return Command::FAILURE;
        }
        
        $this->info("Environment: {$environment}");
        $this->info("Consumer Key: " . substr($consumerKey, 0, 10) . '...');
        $this->info("Consumer Secret: " . substr($consumerSecret, 0, 10) . '...');

        // Test 2: Get access token
        $this->info('2. Testing access token retrieval...');
        try {
            $token = $this->pesapalService->getAccessToken();
            if ($token) {
                $this->info('✅ Access token retrieved successfully');
                $this->info("Token: " . substr($token, 0, 20) . '...');
            } else {
                $this->error('❌ Failed to retrieve access token');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Token retrieval failed: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Test 3: Register IPN (optional)
        $this->info('3. Testing IPN registration...');
        try {
            $ipnUrl = route('pesapal.ipn');
            $this->info("IPN URL: {$ipnUrl}");
            
            $ipnId = $this->pesapalService->registerIpnUrl($ipnUrl);
            if ($ipnId) {
                $this->info("✅ IPN registered successfully: {$ipnId}");
            } else {
                $this->warn("⚠️ IPN registration failed (this might be normal if already registered)");
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ IPN registration error: " . $e->getMessage());
        }

        // Test 4: Test order creation (if user ID provided)
        $userId = $this->option('user-id');
        if ($userId) {
            $this->info('4. Testing order creation...');
            
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return Command::FAILURE;
            }
            
            $this->info("Testing with user: {$user->name} ({$user->email})");
            
            try {
                $orderResponse = $this->pesapalService->createSubscriptionOrder($user, 'monthly', 'TEST_' . time());
                
                $this->info('✅ Test order created successfully');
                $this->info('Response keys: ' . implode(', ', array_keys($orderResponse)));
                
                if (isset($orderResponse['redirect_url'])) {
                    $this->info("Redirect URL: {$orderResponse['redirect_url']}");
                } else {
                    $this->warn("⚠️ No redirect_url in response");
                }
                
                if (isset($orderResponse['order_tracking_id'])) {
                    $this->info("Order Tracking ID: {$orderResponse['order_tracking_id']}");
                } else {
                    $this->warn("⚠️ No order_tracking_id in response");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Order creation failed: " . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->info('4. Skipping order creation test (no --user-id provided)');
            $this->info('To test order creation, run: php artisan pesapal:test --user-id=YOUR_USER_ID');
        }

        $this->info('🎉 All tests completed successfully!');
        return Command::SUCCESS;
    }
}
