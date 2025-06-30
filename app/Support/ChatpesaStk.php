<?php

namespace App\Support;

use Throwable;
use App\Models\MpesaPayment;
use Illuminate\Support\Facades\Log;
use App\Events\PaymentStatusUpdated;
use Illuminate\Support\Facades\Http;
use App\Events\MpesaPaymentSucceeded;

class ChatpesaStk
{
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('chatpesa.CHATPESA_URL');
        $this->apiToken = base64_encode(config('chatpesa.CHATPESA_API_KEY'));
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
        $payload = [
            'mpesa_number'      => $this->formatPhoneNumber($phone),
            'amount'            => $amount,
            'callback_url'      => config('chatpesa.CHATPESA_CALLBACK')
        ];

        try {
            $response = Http::withToken($this->apiToken)
                ->acceptJson()
                ->asJson()
                ->post($this->baseUrl, $payload)
                ->throw()
                ->json('data');

            return MpesaPayment::create([
                'user_id'             => $userId ?? null,
                'purpose'             => $purpose,
                'merchant_request_id' => $response['merchant_id'] ?? null,
                'checkout_request_id' => $response['checkout_id'] ?? null,
                'phone_number'        => $phone ?? null,
                'transaction_date'    => now(),
                'amount'              => $amount,
                'status'              => 'pending',
            ]);
        } catch (Throwable $e) {
            Log::error('STK push failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    /* --------------------------------------------------------------
     |  CALLBACK  →  call from your Route/controller
     |--------------------------------------------------------------*/

    public function handleCallback(array $payload)
    {
        $payment = MpesaPayment::where('checkout_request_id', $payload['checkout_id'])->first();

        $status = $payload['status'] === 'success' ? 'success' : 'failed';

        $payment->update([
            'status'   => $status,
            'reason'   => $payload['reason'] ?? null,
        ]);

        // Let the front-end know immediately (optional but nicer than polling)
        broadcast(new MpesaPaymentSucceeded($payment))->toOthers();
    }

    private function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10 && str_starts_with($digits, '07')) {
            return $digits;
        }

        if (strlen($digits) === 9 && str_starts_with($digits, '7')) {
            return '0' . $digits;
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '2547')) {
            return '0' . substr($digits, 3);
        }

        throw new \InvalidArgumentException(
            "Invalid Kenyan phone number: {$phone} (digits extracted: {$digits})"
        );
    }
}
