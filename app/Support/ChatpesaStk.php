<?php

namespace App\Support;

use Throwable;
use App\Models\MpesaPayment;
use Illuminate\Support\Facades\Log;
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
        Log::info("ChatpesaStk : ", $payload);

        $payment = MpesaPayment::where('checkout_request_id', $payload['checkout_id'] ?? null)->first();

        if (!$payment) {
            $payment = MpesaPayment::create([
                'checkout_request_id' => $payload['checkout_id'] ?? null,
                'merchant_request_id' => $payload['merchant_id'] ?? null,
                'status'              => $payload['status']   ?? 'unknown',
                'reason'              => $payload['reason']   ?? null,
            ]);
        } else {
            $payment->update([
                'status' => $payload['status'],
                'reason' => $payload['reason'],
            ]);
        }
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

    /**
     * Block the current request until the given MpesaPayment is marked
     * “success” or “failed”, or until the timeout (default 60 s) elapses.
     *
     * @param  \App\Models\MpesaPayment  $payment
     * @param  int  $timeout   Seconds to wait before giving up
     * @param  int  $interval  Seconds between DB refreshes
     * @return bool  true  → success    false → failed / timed-out
     */
    public function waitForConfirmation(
        MpesaPayment $payment,
        int $timeout  = 60,
        int $interval = 3
    ): bool {
        // PHP’s default max_execution_time is 30 s; bump it just in case.
        if (function_exists('set_time_limit')) {
            @set_time_limit($timeout + 5);
        }

        $startedAt = microtime(true);

        while (microtime(true) - $startedAt < $timeout) {
            $payment->refresh();                           // pull latest row

            if ($payment->status === 'success') {
                // optionally broadcast here
                // event(new MpesaPaymentSucceeded($payment));
                return true;
            }

            if ($payment->status === 'failed') {
                return false;
            }

            sleep($interval);                              // wait before next check
        }

        // Timed-out
        return false;
    }
}
