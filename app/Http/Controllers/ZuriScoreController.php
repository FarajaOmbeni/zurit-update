<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

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
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => `{
                "api_username" => $username,
                "api_password" => $password
            }`,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function get_report(Request $request)
    {
        $api_url = env('ZURIT_URL');
        $api_username = env('ZURIT_USERNAME');
        $api_password = env('ZURIT_PASSWORD');
        $callback_url = env('ZURIT_CALLBACK_URL');

        $token = $this->authenticate($api_url, $api_username, $api_password);

        dd($token);

        $statement_type = $request->statement_type;
        $statement_password = $request->statement_password;
        $statement_file = $request->statement_file;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$api_url/v1/sta/statement_analysis",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'statement_type' => $statement_type,
                'password' => $statement_password,
                'file' => $statement_file,
                'report_callback_url' => $callback_url
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        dd($response);
    }
}
