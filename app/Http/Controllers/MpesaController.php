<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Mpesa;
use App\Models\MpesaPayment;
use App\Support\MpesaStk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MpesaController extends Controller
{
    public function getAccessToken()
    {
        $consumerKey = env('MPESA_CONSUMER_KEY');
        $consumerSecret = env('MPESA_CONSUMER_SECRET');

        //choose one depending on you development environment
        //sandbox
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        //live
        // $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        try {

            $encodedCredentials = base64_encode($consumerKey . ':' . $consumerSecret);

            $headers = [
                'Authorization: Basic ' . $encodedCredentials,
                'Content-Type: application/json'
            ];

            // Initialize cURL session and set options
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Send the request and parse the response
            $response = json_decode(curl_exec($ch), true);

            // Check for errors and return the access token
            if (curl_errno($ch)) {
                throw new Exception('Failed to get access token: ' . curl_error($ch));
            } else if (isset($response['access_token'])) {
                return $response['access_token'];
            } else {
                throw new Exception('Failed to get access token: ' . $response['error_description']);
            }

            // Close the cURL session
            curl_close($ch);
        } catch (Exception $error) {
            throw new Exception('Failed to get access token.');
        }
    }

    public function sendStkPush()
    {
        $token = $this->getAccessToken();
        $timestamp = date('YmdHis');

        $shortCode = "174379"; //sandbox -174379
        $passkey = env('MPESA_PASS_KEY');

        $stk_password = base64_encode($shortCode . $passkey . $timestamp);

        //choose one depending on you development environment
        //sandbox
        $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        //live
        // $url = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];

        $requestBody = array(
            "BusinessShortCode" => $shortCode,
            "Password" => $stk_password,
            "Timestamp" => $timestamp,
            "TransactionType" => "CustomerPayBillOnline", //till "CustomerBuyGoodsOnline"
            "Amount" => "1",
            "PartyA" => "254708374149",
            "PartyB" => $shortCode,
            "PhoneNumber" => "254729054607",
            "CallBackURL" => env('MPESA_CALLBACK'),
            "AccountReference" => "account",
            "TransactionDesc" => "test"
        );

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            Log::info('STK Push Response: ' . $response);

            return response()->json([
                'status' => 'success',
                'message' => 'Stk sent successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
        }
    }

    public function handleCallback(Request $request, MpesaStk $stk)
    {
        Log::info('🔥 M-PESA Callback Hit!', ['data' => $request->all()]);
        
        $stk->handleCallback($request->all());
        return ['ResultCode' => 0, 'ResultDesc' => 'OK'];
        // // Get the raw POST data from the request
        // $raw_data = $request->getContent();

        // // Decode the JSON data
        // $data = json_decode($raw_data, true) ?? [];

        // $body = data_get($data, 'Body.stkCallback', []);
        // $items = collect(data_get($body, 'CallbackMetadata.Item', []))
        //     ->pluck('Value', 'Name');


        // Log::info("Callback Body: ", $body);

        // if($body['ResultCode'] == 0) {
        //     $record = MpesaPayment::updateOrCreate(
        //         ['checkout_request_id' => $body['CheckoutRequestID'] ?? ''],
        //         [
        //             'merchant_request_id'  => $body['MerchantRequestID'] ?? '',
        //             'result_code'          => $body['ResultCode']        ?? -1,
        //             'result_desc'          => $body['ResultDesc']        ?? 'N/A',

        //             'amount'               => $items->get('Amount'),
        //             'mpesa_receipt_number' => $items->get('MpesaReceiptNumber'),
        //             'balance'              => $items->get('Balance'),
        //             'phone_number'         => $items->get('PhoneNumber'),
        //             'transaction_date'     => $items->get('TransactionDate'),
        //         ]
        //     );
        // } else {
        //     return redirect()->route('zuriscore.index')->withErrors(['error' => 'An error occurred during the transaction.']);
        // }

        // // Log the callback data for debugging
        // Log::info("Callback data received", ['data' => $data]);

        // // Here you can handle the data, e.g., validate and save to the database

        // // Return a success response
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Callback received successfully'
        // ], 200);
    }
}
