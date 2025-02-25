<?php
namespace App\Http\Controllers;

use App\Models\Mpesa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

namespace App\Http\Controllers;

class PaymentController extends Controller
        {
            public function stkpush()
            {
                // Generate a unique transaction ID
                $transactionId = Str::uuid();

                // Get the current user
                $user = Auth::user();

                // Get the phone number from the user
                $phoneNumber = $user->phone_number;

                // Set the payment details
                $amount = 1; // Set the amount to be paid
                $description = 'Payment for C2B'; // Set the payment description

                // Save the transaction details to the database
                $mpesa = new Mpesa();
                $mpesa->transaction_id = $transactionId;
                $mpesa->user_id = $user->id;
                $mpesa->phone_number = $phoneNumber;
                $mpesa->amount = $amount;
                $mpesa->description = $description;
                $mpesa->status = 'pending';
                $mpesa->save();

                // Make the STK push request to Safaricom API
                $accessToken = self::generateSandBoxToken();
                $client = new Client();
                $response = $client->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                    'headers' => [
                        'Authorization' => 'Bearer' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'BusinessShortCode' => '174379',
                        'Password' => 'YOUR_PASSWORD',
                        'Timestamp' => Carbon::now()->format('YmdHis'),
                        'TransactionType' => 'CustomerPayBillOnline',
                        'Amount' => $amount,
                        'PartyA' => $phoneNumber,
                        'PartyB' => '174379',
                        'PhoneNumber' => $phoneNumber,
                        'CallBackURL' => 'YOUR_CALLBACK_URL',
                        'AccountReference' => $transactionId,
                        'TransactionDesc' => $description,
                    ],
                ]);

                // Redirect the user to the payment confirmation page
                return redirect()->route('payment.confirmation');
            }
        }

?>