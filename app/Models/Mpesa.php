<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Mpesa extends Model
{
    use HasFactory;

    protected $consumer_key;
    protected $consumer_secret;
    protected $passkey;
    protected $amount;
    protected $accountReference;
    protected $phone;
    protected $env;
    protected $short_code;
    //protected $parent_short_code;
    protected $initiatorName;
    protected $initiatorPassword ;

    public function __construct(){
        $this->short_code = '174379';
        $this->consumer_key = env('MPESA_CONSUMER_KEY');
        $this->consumer_secret = env('MPESA_CONSUMER_SECRET');
        $this->passkey = env('MPESA_PASSKEY');
        $this->CallBackURL = env('MPESA_CALL_BACK_URL');
        $this->env = env('MPESA_ENV');
        $this->initiatorName = env('MPESA_INITIATOR_NAME');
        $this->initiatorPassword = env('MPESA_INITIATOR_PASSWORD');
    }
    

    /** Lipa na M-PESA password **/
    public function getPassword()
    {
        $timestamp = Carbon::now()->format('YmdHms');
        $password  = base64_encode($this->short_code . $this->passkey . $timestamp);

        return $password;
    }

    public function lipaNaMpesa($amount,$phone,$accountReference){
        $this->phone = $phone;
        $this->amount=$amount;
        $this->accountReference=$accountReference;
    
        $Password = $this->getPassword();
        $Timestamp = Carbon::now()->format('YmdHms');
    
        $headers = ['Content-Type:application/json; charset=utf8'];
    
        $access_token_url = ($this->env  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        $initiate_url = ($this->env == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 


        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $this->consumer_key.':'.$this->consumer_secret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = $result->access_token;
        curl_close($curl);


        # header for stk push
        $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
        # initiating the transaction
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $initiate_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $this->short_code,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $this->amount,
            'PartyA' => $phone,
            'PartyB' => $this->short_code,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->CallBackURL,
            'AccountReference' => $this->accountReference,
            'TransactionDesc' => $phone." has paid ".$this->amount." to ".$this->short_code
        );

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $response = curl_exec($curl);

        return $response;
    }

    public function status($transactionCode){
        $type = 4;
        $command = "TransactionStatusQuery";
        $remarks = "Transaction Status Query"; 
        $occasion = "Transaction Status Query";
        $results_url = "https://localhost:8000/TransactionStatus/result/";
        $timeout_url = "https://localhost:8000/TransactionStatus/queue/"; 

        $access_token = ($this->env == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        $credentials = base64_encode($this->consumer_key . ':' . $this->consumer_secret); 
        
        $ch = curl_init($access_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response); 

        //echo $result->{'access_token'};
        
        $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

        $publicKey = file_get_contents(__DIR__ . "/mpesa_public_cert.cer"); 
        $isvalid = openssl_public_encrypt($this->initiatorPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING); 
        $password = base64_encode($encrypted);

        //echo $token;

        $curl_post_data = array( 
            "Initiator" => $this->initiatorName, 
            "SecurityCredential" => $password, 
            "CommandID" => $command, 
            "TransactionID" => $transactionCode, 
            "PartyA" => $this->short_code, 
            "IdentifierType" => $type, 
            "ResultURL" => $results_url, 
            "QueueTimeOutURL" => $timeout_url, 
            "Remarks" => $remarks, 
            "Occasion" => $occasion,
        ); 

        $data_string = json_encode($curl_post_data);

        //echo $data_string;

        $endpoint = ($env == "live") ? "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 

        $ch2 = curl_init($endpoint);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch2, CURLOPT_POST, 1);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        $response     = curl_exec($ch2);
        curl_close($ch2);

        //echo "Authorization: ". $response;

        $result = json_decode($response); 
        
        return $result;
    }

}
