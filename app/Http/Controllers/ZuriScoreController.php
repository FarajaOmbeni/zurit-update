<?php

namespace App\Http\Controllers;

use CURLFile;
use Inertia\Inertia;
use App\Support\MpesaStk;
use Illuminate\Http\Request;
use App\Mail\ZuriScoreReportMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    public function get_report(Request $request, MpesaStk $stk)
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

        $payment  = $stk->sendStkPush(
            amount: $request->statement_duration,
            phone: $request->phone,
            purpose: 'report',
            userId: auth()->user()->id
        );

        if (! $stk->waitForConfirmation($payment)) {
            return back()->withErrors("Transaction Failed. Please try again.");
        }

        $statement_type = $request->statement_type;
        $statement_password = $request->statement_password;
        $file = $request->statement_file;
        $filePath = $file->getPathname();
        $email = urlencode($request->input('email'));

        $postFields = [
            'statement_type' => $statement_type,
            'password' => $statement_password,
            'file' => new CURLFile($filePath, $file->getMimeType(), $file->getClientOriginalName()),
            'report_callback_url' => $callback_url . '?email=' . $email
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
        return to_route('zuriscore.index');
    }

    public function handleCallback(Request $request)
    {
        $email = $request->query('email');
        $data = $request->all();
        $reportUrl = $data['reportUrl'];
        $fullName = explode(' ', $data['reportData']['name']);
        $firstName = $fullName[0];

        Log::info('Callback for email:', ['email' => $email, 'name'=>$firstName,'reportUrl' => $reportUrl]);
        Log::info('ALL ZEE DATA:', ['data' => $data]);

        Mail::to($email)->send(new ZuriScoreReportMail($firstName, $reportUrl));

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Callback received successfully'
        ], 200);
    }
}
