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
        if (env('MPESA_ENVIRONMENT') === 'live') {
            $this->baseUrl  = 'https://api.safaricom.co.ke';
            $this->shortCode = env('MPESA_SHORTCODE');
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
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode(
            $this->shortCode . env('MPESA_PASSKEY') . $timestamp
        );

        $payload = [
            'BusinessShortCode' => $this->shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerBuyGoodsOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $this->shortCode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => env('MPESA_CALLBACK'),
            'AccountReference'  => 'account',
            'TransactionDesc'   => $purpose,
        ];

        $token = $this->accessToken();
        Log::info($token);

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
        if ($payment->isSuccessful()) {
            event(new MpesaPaymentSucceeded($payment));
        }
    }

    /* --------------------------------------------------------------*/
    private function accessToken(): string
    {
        if ($this->token) return $this->token;   // cached

        $credentials = base64_encode(
            env('MPESA_CONSUMER_KEY') . ':' .
                env('MPESA_CONSUMER_SECRET')
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
            $this->shortCode . env('MPESA_PASSKEY') . $timestamp
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
}
