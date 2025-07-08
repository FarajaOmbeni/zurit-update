<?php

namespace App\Support;

use Illuminate\Support\Str;
use App\Models\MpesaPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Events\MpesaPaymentSucceeded;

class MpesaStk
{
    public function __construct(
        private string $shortCode  = '174379',                     
        private string $baseUrl    = 'https://sandbox.safaricom.co.ke',
        private ?string $token     = null,                         
    ) {
        if (config('mpesa.MPESA_ENVIRONMENT') === 'live') {
            $this->baseUrl  = 'https://api.safaricom.co.ke';
            $this->shortCode = config('mpesa.MPESA_SHORTCODE');
        }
    }

    /* --------------------------------------------------------------
     |  PUBLIC API  →  $stk->sendStkPush(...)
     |--------------------------------------------------------------*/

    public function sendStkPush(
        int       $amount,
        string    $phone,               
        string    $purpose   = 'general',
        ?int      $userId    = null
    ): MpesaPayment {
        $timestamp = date('YmdHis');
        $password  = base64_encode(
            $this->shortCode . config('mpesa.MPESA_PASSKEY') . $timestamp
        );

        $payload = [
            'BusinessShortCode' => $this->shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline', //CustomerBuyGoodsOnline
            'Amount'            => $amount,
            'PartyA'            => $this->formatPhoneNumber($phone),
            'PartyB'            => $this->shortCode,
            'PhoneNumber'       => $this->formatPhoneNumber($phone),
            'CallBackURL'       => config('mpesa.MPESA_CALLBACK'),
            'AccountReference'  => 'account',
            'TransactionDesc'   => 'test',
        ];

        $token = $this->accessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", $payload)
            ->throw()
            ->json();

        return MpesaPayment::create([
            'user_id'             => $userId,
            'purpose'             => $purpose,
            'merchant_request_id' => $response['MerchantRequestID'] ?? null,
            'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
            'phone_number'        => $phone,
            'amount'              => $amount,
        ]);
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters (e.g. +, spaces)
        $phone = preg_replace('/\D/', '', $phone);

        // Handle formats
        if (strlen($phone) === 9 && str_starts_with($phone, '7')) {
            // e.g. 729054607
            return '254' . $phone;
        }

        if (strlen($phone) === 10 && str_starts_with($phone, '07')) {
            // e.g. 0729054607
            return '254' . substr($phone, 1);
        }

        if (strlen($phone) === 13 && str_starts_with($phone, '254')) {
            // e.g. 254729054607
            return $phone;
        }

        if (strlen($phone) === 12 && str_starts_with($phone, '254')) {
            // Already formatted
            return $phone;
        }

        throw new \InvalidArgumentException("Invalid Kenyan phone number format: {$phone}");
    }


    /* --------------------------------------------------------------
     |  CALLBACK  →  call from your Route/controller
     |--------------------------------------------------------------*/
    public function handleCallback(array $payload): void
    {
        $body  = data_get($payload, 'Body.stkCallback', []);
        $items = collect(data_get($body, 'CallbackMetadata.Item', []))
            ->pluck('Value', 'Name');

        $payment = MpesaPayment::where(
            'checkout_request_id',
            $body['CheckoutRequestID'] ?? ''
        )->first();

        if (! $payment) {
            Log::warning('Unknown M-PESA callback', $body);
            return;
        }

        $payment->update([
            'result_code'          => $body['ResultCode'],
            'result_desc'          => $body['ResultDesc'],
            'amount'               => $items->get('Amount'),
            'mpesa_receipt_number' => $items->get('MpesaReceiptNumber'),
            'phone_number'         => $items->get('PhoneNumber'),
            'transaction_date'     => $items->get('TransactionDate'),
        ]);

        // Optionally fire a single event if successful
        // if ($payment->isSuccessful()) {
        //     event(new MpesaPaymentSucceeded($payment));
        // }
    }

    /* --------------------------------------------------------------*/
    private function accessToken(): string
    {
        if ($this->token) return $this->token;   // cached

        $credentials = base64_encode(
            config('mpesa.MPESA_CONSUMER_KEY') . ':' .
                config('mpesa.MPESA_CONSUMER_SECRET')
        );

        $this->token = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials
        ])
            ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials")
            ->throw()
            ->json('access_token');

        return $this->token;
    }

    public function checkStkStatus(string $checkoutRequestId): array
    {
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode(
            $this->shortCode . config('mpesa.MPESA_PASSKEY') . $timestamp
        );

        $payload = [
            'BusinessShortCode' => $this->shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        return Http::withToken($this->accessToken())
            ->acceptJson()
            ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", $payload)
            ->throw()
            ->json();
    }

    public function waitForConfirmation(MpesaPayment $payment, int $attempts = 6, int $delaySeconds = 10): bool
    {
        for ($i = 0; $i < $attempts; $i++) {
            sleep($delaySeconds);

            $status = $this->checkStkStatus($payment->checkout_request_id);

            if ($status['ResultCode'] == 0) {
                return true;
            } else {
                $payment->update([
                    'result_code' => $status['ResultCode'],
                    'result_desc' => $status['ResultDesc']
                ]);

                return false;
            }
        }

        // Timeout after all attempts
        $payment->update([
            'result_code' => 408,
            'result_desc' => 'Timeout: No user response within ' . ($attempts * $delaySeconds) . ' seconds'
        ]);

        return false;
    }
}
