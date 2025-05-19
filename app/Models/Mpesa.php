<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mpesa extends Model
{
    protected $fillable = [
        'merchant_request_id',
        'checkout_request_id',
        'result_code',
        'result_desc',
        'phone_number',
        'amount',
        'mpesa_receipt_number',
        'balance',
        'transaction_date',
    ];
}
