<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaPayment extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_request_id',
        'checkout_request_id',
        'reason',
        'status',
        'phone_number',
        'amount',
        'mpesa_receipt_number',
        'transaction_date',
        'purpose',
    ];

    protected function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful(): bool
    {
        return $this->result_code == 0;
    }
}
