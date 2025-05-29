<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Support\MpesaStk;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Mail\SubscriptionMail;
use App\Mail\UserSubscriptionMail;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{

    public function index()
    {
        return Inertia::render('UserDashboard/Subscription');
    }

    public function subscribe(Request $request, MpesaStk $stk) {
        $request->validate([
            'phone' => 'required'
        ]);

        $phone = $request->phone;
        $user = auth()->user();
        $name = $user->name;

        $payment  = $stk->sendStkPush(
            amount: 1,
            phone: $phone,
            purpose: 'subscription',
            userId: $user->id
        );

        if (! $stk->waitForConfirmation($payment)) {
            return back()->withErrors("Transaction Failed. Please try again.");
        }

        //Email to admin
        Mail::to('ombenifaraja@gmail.com')->send(new SubscriptionMail($name, $phone));
        //Email to user
        Mail::to('ombenifaraja2000@gmail.com')->send(new UserSubscriptionMail($name));

        return to_route('budget.index');
    }
}
