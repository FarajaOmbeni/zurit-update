<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Support\ChatpesaStk;
use App\Exceptions\InvalidPhoneNumberException;

class ElearningPaywallController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Elearning/Paywall', [
            'phone' => $user->phone_number,
            'price' => (int) (config('elearning.price', 8000)),
        ]);
    }

    public function pay(Request $request, ChatpesaStk $stk)
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $amount = (int) (config('elearning.price', 8000));

        try {
            $payment = $stk->sendStkPush(
                amount: $amount,
                phone: $request->input('phone'),
                purpose: 'elearning',
                userId: Auth::id()
            );

            Cache::put("payment_data_{$payment->id}", [
                'type' => 'elearning',
            ], now()->addMinutes(15));
        } catch (InvalidPhoneNumberException $e) {
            return back()->withErrors(['phone' => $e->getMessage()]);
        }

        return redirect()->route('elearning.processing', ['payment' => $payment->id]);
    }

    public function processing(Request $request, \App\Models\MpesaPayment $payment)
    {
        $this->authorizePayment($payment);

        $redirectTo = session('elearning.intended_url') ?? route('elearning.landing');

        return Inertia::render('Elearning/Processing', [
            'payment' => $payment,
            'phone' => $payment->phone_number,
            'redirectTo' => $redirectTo,
        ]);
    }

    protected function authorizePayment(\App\Models\MpesaPayment $payment): void
    {
        if (Auth::id() !== $payment->user_id) {
            abort(403);
        }
    }
}

