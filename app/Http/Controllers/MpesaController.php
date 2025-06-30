<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\MpesaPayment;
use App\Support\ChatpesaStk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function handleCallback(Request $request, ChatpesaStk $stk)
    {
        try {
            $stk->handleCallback($request->all());
            return response()->json(['status' => 'success']);
        } catch (Throwable $e) {
            Log::error('Callback processing failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function status(MpesaPayment $payment)
    {
        return response()->json([
            'status'   => $payment->status,          // pending | success | failed
            'reason'   => $payment->reason,
            'checksum' => $payment->updated_at->timestamp, // lets the UI short-circuit if nothing changed
        ]);
    }
}