<?php

namespace App\Support;

use App\Models\MpesaPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

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
            'callback_url'      =>config('chatpesa.CHATPESA_CALLBACK')
        ];

        dd($this->apiToken);

        $response = Http::withToken($this->apiToken)
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl, $payload)
            ->throw();

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
    public function handleCallback(Request $request)
    {
        $data = $request->all();

        Log::info('ALL ZEE DATA:', ['data' => $data]);

        // Mail::to($email)->send(new ZuriScoreReportMail($firstName, $reportUrl));

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Callback received successfully'
        ], 200);
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
}
