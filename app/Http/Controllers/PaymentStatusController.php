<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function processing(MpesaPayment $payment)
    {
        return Inertia::render('Payments/Processing', [
            'paymentId' => $payment->id,
            'amount'    => $payment->amount,
            'title'     => $payment->purpose,
        ]);
    }

    public function status(MpesaPayment $payment)
    {
        return response()->json([
            'status' => $payment->status,
            'reason' => $payment->reason,
        ]);
    }
}
