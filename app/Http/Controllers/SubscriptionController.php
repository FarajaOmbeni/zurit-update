<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscriptions',
        ]);

        Subscription::create([
            'email' => $request->input('email'),
        ]);

        return redirect()->back()->with('success', 'Subscription successful!');
    }

    public function index()
    {
        $subscriptions = Subscription::latest()->get();

        return view('subscription_admindash', compact('subscriptions'));
    }
}
