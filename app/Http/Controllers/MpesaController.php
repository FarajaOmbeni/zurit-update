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
}