<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Support\ChatpesaStk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ElearningPurchaseController extends Controller
{
    public function buy(Request $request, Course $course, ChatpesaStk $stk)
    {
        $request->validate([
            'phone' => ['nullable', 'string'],
        ]);

        $phone = $request->input('phone') ?: (Auth::user()->phone_number ?? '');
        if (!$phone) {
            return back()->withErrors(['phone' => 'Please add a phone number to your profile or enter one to continue.']);
        }

        $amount = (int) ($course->price ?? 0);
        if ($amount <= 0) {
            return back()->withErrors(['course' => 'This course is not available for purchase yet.']);
        }

        $payment = $stk->sendStkPush(
            amount: $amount,
            phone: $phone,
            purpose: 'elearning',
            userId: Auth::id()
        );

        Cache::put("payment_data_{$payment->id}", [
            'type' => 'elearning',
            'course_id' => $course->id,
        ], now()->addMinutes(15));

        return redirect()->route('elearning.processing', ['payment' => $payment->id]);
    }

    public function processing(\App\Models\MpesaPayment $payment)
    {
        if (Auth::id() !== $payment->user_id) {
            abort(403);
        }

        $redirectTo = session('elearning.intended_url') ?? route('elearning.courses');

        return Inertia::render('Elearning/Processing', [
            'payment' => $payment,
            'phone' => $payment->phone_number,
            'redirectTo' => $redirectTo,
        ]);
    }
}

