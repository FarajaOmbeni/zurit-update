<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use CURLFile;
use Inertia\Inertia;
use App\Support\MpesaStk;
use Illuminate\Http\Request;
use App\Mail\ZuriScoreReportMail;
use App\Support\ChatpesaStk;
use App\Exceptions\InvalidPhoneNumberException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ZuriScoreController extends Controller
{
    public function index()
    {
        return Inertia::render('UserDashboard/ZuriScore');
    }

    public function authenticate($url, $username, $password)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url/v1/sta/auth",
            // Add these two lines:
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30, // Set a reasonable timeout
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                "api_username" => $username,
                "api_password" => $password
            ]),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            Log::error("cURL Error: " . $err);
            return false;
        }

        if ($httpCode !== 200) {
            Log::error("HTTP Error: " . $httpCode . " Response: " . $response);
            return false;
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['jwt'])) {
            return $responseData['jwt'];
        }

        Log::error("Invalid response format: " . $response);
        return false;
    }

    public function get_report(Request $request, ChatpesaStk $stk)
    {
        $api_url = config('zuriscore.ZURIT_URL');
        $api_username = config('zuriscore.ZURIT_USERNAME');
        $api_password = config('zuriscore.ZURIT_PASSWORD');
        $callback_url = config('zuriscore.ZURIT_CALLBACK_URL');

        $token = $this->authenticate($api_url, $api_username, $api_password);

        $request->validate([
            'statement_type' => 'required|string',
            'statement_password' => 'nullable|string',
            'statement_duration' => 'required|integer',
            'statement_file' => 'required|file|mimes:pdf|max:2048',
            'email' => 'required|email',
            'email_confirmation' => 'required|same:email',
            'phone' => 'required'
        ]);

        try {
            $payment  = $stk->sendStkPush(
                amount: $request->statement_duration,
                phone: $request->phone,
                purpose: 'report',
                userId: Auth::id()
            );

            Cache::put("payment_data_{$payment->id}", [
                'type' => 'zuriscore',
                'phone' => $request->phone,
                'email' => $request->email,
            ], now()->addMinutes(10));
        } catch (InvalidPhoneNumberException $e) {
            return back()->withErrors(['phone' => $e->getMessage()]);
        }



        $statement_type = $request->statement_type;
        $statement_password = $request->statement_password;
        $file = $request->statement_file;
        $filePath = $file->getPathname();
        $email = urlencode($request->input('email'));
        $new_callback_url = $callback_url . '?email=' . $email . '&payment_id=' . $payment->id;

        $postFields = [
            'statement_type' => $statement_type,
            'password' => $statement_password,
            'file' => new CURLFile($filePath, $file->getMimeType(), $file->getClientOriginalName()),
            'report_callback_url' => $new_callback_url
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$api_url/v1/sta/statement_analysis",
            // Add these two lines:
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $responseData = json_decode($response);
        curl_close($curl);

        if (isset($responseData->code)) {
            return to_route('zuriscore.index')->withErrors($responseData->message);
        }

        // Redirect to processing page with payment ID
        return redirect()->route('zuriscore.processing', ['payment_id' => $payment->id]);
    }

    public function handleCallback(Request $request)
    {
        $email = $request->query('email');
        $payment_id = $request->query('payment_id');
        $data = $request->all();

        Log::info('ZuriScore callback received:', [
            'email' => $email,
            'payment_id' => $payment_id,
            'data' => $data
        ]);

        if (!$payment_id) {
            Log::error('No payment_id in ZuriScore callback');
            return response()->json(['status' => 'error', 'message' => 'No payment_id provided'], 400);
        }

        $reportUrl = $data['reportUrl'] ?? null;
        $fullName = explode(' ', $data['reportData']['name'] ?? '');
        $firstName = $fullName[0] ?? '';

        if (!$reportUrl || !$firstName) {
            Log::error('Missing reportUrl or name in ZuriScore callback', [
                'reportUrl' => $reportUrl,
                'firstName' => $firstName
            ]);
            return response()->json(['status' => 'error', 'message' => 'Missing required data'], 400);
        }

        $cacheKey = "payment_data_{$payment_id}";
        $paymentData = Cache::get($cacheKey) ?? [];

        Log::info('Existing payment data:', ['cacheKey' => $cacheKey, 'paymentData' => $paymentData]);

        $paymentData = array_merge($paymentData, [
            'name' => $firstName,
            'report_url' => $reportUrl,
        ]);

        Cache::put($cacheKey, $paymentData, now()->addMinutes(10));

        Log::info('Updated payment data:', ['cacheKey' => $cacheKey, 'paymentData' => $paymentData]);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Callback received successfully'
        ], 200);
    }

    public function processing($payment_id)
    {
        $payment = \App\Models\MpesaPayment::findOrFail($payment_id);

        // Ensure user can only view their own payments
        if (Auth::id() !== $payment->user_id) {
            abort(403);
        }

        return Inertia::render('UserDashboard/ZuriScoreProcessing', [
            'payment' => $payment,
            'phone' => $payment->phone_number
        ]);
    }

    public function checkPaymentStatus($payment_id)
    {
        $payment = \App\Models\MpesaPayment::findOrFail($payment_id);

        // Ensure user can only check their own payments
        if (Auth::id() !== $payment->user_id) {
            abort(403);
        }

        return response()->json([
            'status' => $payment->status,
            'reason' => $payment->reason,
            'payment_id' => $payment->id
        ]);
    }
}
