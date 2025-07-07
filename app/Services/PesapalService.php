<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PesapalService
{
    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;

    public function __construct()
    {
        $this->baseUrl = config('pesapal.PESAPAL_ENVIRONMENT') === 'sandbox'
            ? 'https://cybqa.pesapal.com/pesapalv3'
            : 'https://pay.pesapal.com/v3';

        $this->consumerKey = config('pesapal.PESAPAL_CONSUMER_KEY');
        $this->consumerSecret = config('pesapal.PESAPAL_CONSUMER_SECRET');
    }

    /**
     * Get access token from Pesapal
     */
    public function getAccessToken($forceRefresh = false)
    {
        // Check cache first (unless force refresh)
        if (!$forceRefresh) {
            $token = Cache::get('pesapal_access_token');
            if ($token) {
                return $token;
            }
        }

        return $this->requestFreshToken();
    }

    /**
     * Request a fresh access token from Pesapal
     */
    private function requestFreshToken()
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/api/Auth/RequestToken', [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['token'])) {
                    Log::error('Pesapal token response missing token field', ['response' => $data]);
                    return null;
                }

                $token = $data['token'];

                // Cache token for 23 hours (expires in 24 hours)
                Cache::put('pesapal_access_token', $token, now()->addHours(23));

                Log::info('Fresh Pesapal token obtained successfully');
                return $token;
            }

            Log::error('Pesapal token request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Pesapal token request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Register IPN URL
     */
    public function registerIpnUrl($ipnUrl)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
                'url' => $ipnUrl,
                'ipn_notification_type' => 'GET'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['ipn_id'] ?? null;
            }

            Log::error('Pesapal IPN registration failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Pesapal IPN registration exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Submit order for payment
     */
    public function submitOrder($orderData)
    {
        // Ensure IPN is registered first (this also validates our token)
        $ipnId = $this->getOrRegisterIpn();
        if (!$ipnId) {
            throw new \Exception('Unable to register IPN URL');
        }

        // Get a fresh token for the order submission
        $token = $this->getAccessToken();
        if (!$token) {
            throw new \Exception('Unable to get Pesapal access token');
        }

        try {
            // Validate required fields
            if (empty($orderData['email']) || empty($orderData['first_name'])) {
                throw new \Exception('Missing required billing information');
            }

            $payload = [
                'id' => $orderData['order_id'],
                'currency' => 'KES',
                'amount' => (float) $orderData['amount'],
                'description' => $orderData['description'],
                'callback_url' => $orderData['callback_url'],
                'notification_id' => $ipnId,
                'billing_address' => [
                    'email_address' => $orderData['email'],
                    'phone_number' => $orderData['phone'] ?: null,
                    'country_code' => 'KE',
                    'first_name' => $orderData['first_name'],
                    'last_name' => $orderData['last_name'] ?: 'User',
                    'line_1' => $orderData['address'] ?: 'Nairobi',
                    'line_2' => '',
                    'city' => 'Nairobi',
                    'state' => 'Nairobi',
                    'postal_code' => '00100',
                    'zip_code' => '00100'
                ]
            ];

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/api/Transactions/SubmitOrderRequest', $payload);

            // If unauthorized, try with a fresh token
            if ($response->status() === 401) {
                Log::info('Pesapal order failed with 401, retrying with fresh token');

                $token = $this->getAccessToken(true); // Force refresh
                if (!$token) {
                    throw new \Exception('Unable to get fresh access token');
                }

                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ])->post($this->baseUrl . '/api/Transactions/SubmitOrderRequest', $payload);
            }

            if ($response->successful()) {
                $responseData = $response->json();

                // Log the successful response for debugging
                Log::info('Pesapal order submission successful', [
                    'response' => $responseData,
                    'payload' => $payload
                ]);

                return $responseData;
            }

            Log::error('Pesapal order submission failed', [
                'response' => $response->body(),
                'payload' => $payload,
                'status_code' => $response->status()
            ]);
            throw new \Exception('Payment request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Pesapal order submission exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus($orderTrackingId)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($this->baseUrl . '/api/Transactions/GetTransactionStatus', [
                'orderTrackingId' => $orderTrackingId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Pesapal status check failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Pesapal status check exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get or register IPN URL
     */
    private function getOrRegisterIpn()
    {
        // Check cache first
        $ipnId = Cache::get('pesapal_ipn_id');
        if ($ipnId) {
            return $ipnId;
        }

        // Register new IPN
        $ipnUrl = route('pesapal.ipn');
        $ipnId = $this->registerIpnUrl($ipnUrl);

        if ($ipnId) {
            // Cache IPN ID for 30 days
            Cache::put('pesapal_ipn_id', $ipnId, now()->addDays(30));
        }

        return $ipnId;
    }

    /**
     * Create subscription order
     */
    public function createSubscriptionOrder($user, $package, $orderId = null)
    {
        $packages = [
            'monthly' => ['price' => 500, 'duration_days' => 31, 'name' => 'Monthly Subscription'],
            'yearly' => ['price' => 4500, 'duration_days' => 365, 'name' => 'Yearly Subscription']
        ];

        if (!isset($packages[$package])) {
            throw new \Exception('Invalid subscription package');
        }

        $packageData = $packages[$package];
        $orderId = $orderId ?: 'SUB_' . strtoupper(uniqid());

        // Clean and format phone number
        $phone = $user->phone_number ?? '';
        if ($phone && !str_starts_with($phone, '+254')) {
            $phone = preg_replace('/^0/', '254', $phone);
            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }
        }

        // Split name safely
        $nameParts = explode(' ', trim($user->name), 2);
        $firstName = $nameParts[0] ?? $user->name;
        $lastName = $nameParts[1] ?? '';

        $orderData = [
            'order_id' => $orderId,
            'amount' => $packageData['price'],
            'description' => $packageData['name'] . ' - Zurit Prosperity Tools',
            'callback_url' => config('pesapal.PESAPAL_CALLBACK_URL'),
            'email' => $user->email,
            'phone' => $phone,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => 'Nairobi, Kenya'
        ];

        return $this->submitOrder($orderData);
    }
}
