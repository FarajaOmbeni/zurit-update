<?php

namespace App\Events;

use App\Models\MpesaPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MpesaPaymentSucceeded
{
    use Dispatchable, SerializesModels;

    /** The paid MpesaPayment instance */
    public MpesaPayment $payment;

    /**
     * Create a new event instance.
     *
     * @param  MpesaPayment  $payment
     */
    public function __construct(MpesaPayment $payment)
    {
        $this->payment = $payment;
    }
}
