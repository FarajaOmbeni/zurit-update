<?php

namespace App\Support;

use Throwable;
use App\Mail\BuyBookMail;
use App\Models\MpesaPayment;
use App\Mail\UserBuyBookMail;
use App\Mail\ZuriScoreAdminMail;
use App\Mail\ZuriScoreReportMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\InvalidPhoneNumberException;

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

    // ChatpesaStk.php
    public function handleCallback(array $payload)
    {
        Log::info("ChatpesaStk callback:", ['payload: ' => $payload]);

        $payment = MpesaPayment::where('checkout_request_id', $payload['checkout_id'] ?? null)->first();
        Log::info("Payment ID", ['payment_id' => $payment->id]);

        if (!$payment) {
            Log::error('Payment not found for callback', ['payload' => $payload]);
            return;
        }

        $payment->update([
            'status' => $payload['status'],
            'reason' => $payload['reason'] ?? null,
        ]);

        if ($payload['status'] === 'succeeded') {
            try {
                $cacheKey = "payment_data_{$payment->id}";
                $paymentData = Cache::get($cacheKey);

                Log::info("Payment data in callback", ["payment_data_{$payment->id}" => $paymentData]);


                if (!$paymentData) {
                    Log::info("Payment data not found for callback", ["payment_data_{$payment->id}" => $paymentData]);
                    return;
                }

                switch ($paymentData['type']) {
                    case 'book':
                        Log::info("Processing book purchase:", ['payment_data' => $paymentData]);
                        // Fallback data if session missing
                        $name = $paymentData['name'];
                        $email = $paymentData['email'];
                        $phone = $paymentData['phone'] ?? $payment->phone_number;
                        $address = $paymentData['address'];
                        $bookTitle = $paymentData['book_title'];

                        // Admin email with full details
                        Mail::to(config('services.email.admin_email'))->send(new BuyBookMail(
                            $name,
                            $email,
                            $bookTitle,
                            $phone,
                            $address,
                        ));

                        // Customer email with confirmation
                        Mail::to($email)->send(new UserBuyBookMail(
                            $name,
                            $email,
                            $bookTitle,
                            $phone
                        ));
                        break;

                    case 'zuriscore':
                        sleep(5);
                        Log::info("Processing zuriscore purchase:", ['payment_data' => $paymentData]);
                        $name = $paymentData['name'];
                        $email = $paymentData['email'];
                        $reportUrl = $paymentData['report_url'];
                        $reportDate = $paymentData['report_date'] ?? now()->format('Y-m-d');
                        $reportMonths = $paymentData['report_months'] ?? 'N/A';
                        Mail::to($email)->send(new ZuriScoreReportMail($name, $reportUrl));
                        Mail::to(config('services.email.admin_email'))->send(new ZuriScoreAdminMail($name, $reportDate, $reportMonths));
                        break;
                }

                Cache::forget($cacheKey);
                Log::info("Payment data after session: ", ['session' => Cache::get($cacheKey)]);
                Log::info("Emails sent for payment: ", ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Email sending failed: ', ['error' => $e->getMessage()]);
            }
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10 && (str_starts_with($digits, '07') || str_starts_with($digits, '01'))) {
            return $digits;
        }

        if (strlen($digits) === 9 && (str_starts_with($digits, '7') || str_starts_with($digits, '1'))) {
            return '0' . $digits;
        }

        if (strlen($digits) === 12 && (str_starts_with($digits, '2547') || str_starts_with($digits, '2541'))) {
            return '0' . substr($digits, 3);
        }

        throw new InvalidPhoneNumberException($phone, $digits);
    }
}
