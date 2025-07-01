<?php

namespace App\Support;

use Throwable;
use App\Mail\BuyBookMail;
use App\Models\MpesaPayment;
use App\Mail\UserBuyBookMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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

    // public function handleCallback(array $payload)
    // {
    //     Log::info("ChatpesaStk : ", $payload);

    //     $payment = MpesaPayment::where('checkout_request_id', $payload['checkout_id'] ?? null)->first();

    //     if (!$payment) {
    //         $payment = MpesaPayment::create([
    //             'checkout_request_id' => $payload['checkout_id'] ?? null,
    //             'merchant_request_id' => $payload['merchant_id'] ?? null,
    //             'status'              => $payload['status']   ?? 'unknown',
    //             'reason'              => $payload['reason']   ?? null,
    //         ]);
    //     } else {
    //         $payment->update([
    //             'status' => $payload['status'],
    //             'reason' => $payload['reason'],
    //         ]);
    //     }
    // }

    public function handleCallback(array $payload)
    {
        Log::info("ChatpesaStk callback:", $payload);

        $payment = MpesaPayment::where('checkout_request_id', $payload['checkout_id'] ?? null)->first();

        if (!$payment) {
            Log::error('Payment not found for callback', ['payload' => $payload]);
            return;
        }

        $payment->update([
            'status' => $payload['status'],
            'reason' => $payload['reason'] ?? null,
        ]);

        // Send emails only for successful payments
        if ($payload['status'] === 'succeeded') {
            try {
                // Admin email
                Mail::to('ombenifaraja@gmail.com')->send(new BuyBookMail(
                    $payment->amount,
                    $payment->amount,
                    $payment->amount,
                    $payment->amount,
                    $payment->amount
                ));
                $customer_email = 'ombenifaraja2000@gmail.com';

                // Customer email
                Mail::to($customer_email)->send(new UserBuyBookMail(
                    $payment->amount,
                    $payment->amount,
                    $payment->amount,
                    $payment->amount
                ));

                Log::info("Confirmation emails sent for payment: " . $payment->id);
            } catch (\Exception $e) {
                Log::error('Email sending failed: ' . $e->getMessage());
            }
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
}
