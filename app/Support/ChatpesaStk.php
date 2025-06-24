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


        $response = Http::withToken($this->apiToken)
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl, $payload)
            ->throw();

        Log::info('RESPONSE:', ['data' => $response]);

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
        $digits = preg_replace('/\D/', '', $phone);

        // 07 XXXXXXXX  (already OK)
        if (strlen($digits) === 10 && str_starts_with($digits, '07')) {
            return $digits;
        }

        // 7 XXXXXXXX   → prefix 0
        if (strlen($digits) === 9 && str_starts_with($digits, '7')) {
            return '0' . $digits;
        }

        // 2547 XXXXXXXX or +2547… → drop country code, prefix 0
        if (strlen($digits) === 12 && str_starts_with($digits, '2547')) {
            return '0' . substr($digits, 3);   // keep the 7XXXXXXXX part
        }

        throw new \InvalidArgumentException("Invalid Kenyan phone number: {$phone}");
    }
}
