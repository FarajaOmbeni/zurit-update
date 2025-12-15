<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Contact;
use App\Models\Testimonial;
use App\Models\Subscription;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
	public function users()
	{
		$users = User::all()->map(function ($user) {
			return [
				'id' => $user->id,
				'name' => $user->name,
				'email' => $user->email,
				'phone_number' => $user->phone_number,
				'last_login' => $user->last_login ? $user->last_login->format('d-m-Y') : 'Never',
				'subscription' => $user->subscription_expires_at ? $user->subscription_expires_at->format('d-m-Y') : 'Not Subscribed',
			];
		});

		return Inertia::render('Admin/Users', [
			'users' => $users,
		]);
	}

	public function system()
	{
		$users = User::count();
		$blogs = Blog::count();

		// Count users who have actually paid and subscribed (based on user subscription status)
		$subscribedUsers = User::where('subscription_status', 'active')
			->where('subscription_expires_at', '>', now())
			->whereIn('subscription_package', ['monthly', 'yearly'])
			->count();

		// Count subscribers by package type
		$monthlySubscribers = User::where('subscription_status', 'active')
			->where('subscription_expires_at', '>', now())
			->where('subscription_package', 'monthly')
			->count();

		$yearlySubscribers = User::where('subscription_status', 'active')
			->where('subscription_expires_at', '>', now())
			->where('subscription_package', 'yearly')
			->count();

		// Count users on free trial
		$trialUsers = User::where('subscription_status', 'active')
			->where('subscription_expires_at', '>', now())
			->where('subscription_package', 'trial')
			->count();

		// Calculate revenue based on user subscriptions
		$monthlyRevenue = $monthlySubscribers * 500; // KES 500 per month
		$yearlyRevenue = $yearlySubscribers * 4500; // KES 4,500 per year
		$mrrEquivalent = $monthlyRevenue + ($yearlyRevenue / 12); // Monthly recurring revenue equivalent

		// Calculate total annual revenue
		$totalAnnualRevenue = ($monthlyRevenue * 12) + $yearlyRevenue;

		// Mpesa Payment statistics
		$totalPayments = MpesaPayment::count();
		$successfulPayments = MpesaPayment::where('status', 'succeeded')->count();
		$pendingPayments = MpesaPayment::where('status', 'pending')->count();
		$failedPayments = MpesaPayment::where('status', 'failed')->count();

		// Payment revenue by purpose
		$bookPayments = MpesaPayment::where('purpose', 'like', '%book%')->where('status', 'succeeded');
		$zuriscorePayments = MpesaPayment::where('purpose', 'report')->where('status', 'succeeded');
		$subscriptionPayments = MpesaPayment::where('purpose', 'subscription')->where('status', 'succeeded');
		$elearningPayments = MpesaPayment::where('purpose', 'elearning')->where('status', 'succeeded');

		$bookRevenue = $bookPayments->sum('amount');
		$zuriscoreRevenue = $zuriscorePayments->sum('amount');
		$subscriptionRevenue = $subscriptionPayments->sum('amount');
		$elearningRevenue = $elearningPayments->sum('amount');
		$totalPaymentRevenue = $bookRevenue + $zuriscoreRevenue + $subscriptionRevenue + $elearningRevenue;

		// Payment counts by purpose
		$bookPaymentCount = $bookPayments->count();
		$zuriscorePaymentCount = $zuriscorePayments->count();
		$subscriptionPaymentCount = $subscriptionPayments->count();
		$elearningPaymentCount = $elearningPayments->count();

		// Subscription overall statistics (from Subscription model for reference)
		$totalSubscriptions = Subscription::count();
		$activeSubscriptions = Subscription::active()->count();

		return Inertia::render('Admin/System', [
			'users' => $users,
			'blogs' => $blogs,
			// Show users who have actually paid and subscribed
			'subscribed' => $subscribedUsers,
			// Package breakdown
			'monthlySubscribers' => $monthlySubscribers,
			'yearlySubscribers' => $yearlySubscribers,
			'trialUsers' => $trialUsers,
			// Revenue calculations
			'monthlyRevenue' => (float) $monthlyRevenue,
			'yearlyRevenue' => (float) $yearlyRevenue,
			'totalAnnualRevenue' => (float) $totalAnnualRevenue,
			'mrrEquivalent' => (float) $mrrEquivalent,
			// Mpesa Payment statistics
			'totalPayments' => $totalPayments,
			'successfulPayments' => $successfulPayments,
			'pendingPayments' => $pendingPayments,
			'failedPayments' => $failedPayments,
			'bookRevenue' => (float) $bookRevenue,
			'zuriscoreRevenue' => (float) $zuriscoreRevenue,
			'subscriptionRevenue' => (float) $subscriptionRevenue,
			'elearningRevenue' => (float) $elearningRevenue,
			'totalPaymentRevenue' => (float) $totalPaymentRevenue,
			'bookPaymentCount' => $bookPaymentCount,
			'zuriscorePaymentCount' => $zuriscorePaymentCount,
			'subscriptionPaymentCount' => $subscriptionPaymentCount,
			'elearningPaymentCount' => $elearningPaymentCount,
			// Subscription model stats (for reference)
			'totalSubscriptions' => $totalSubscriptions,
			'activeSubscriptions' => $activeSubscriptions,
		]);
	}

	public function messages()
	{
		$messages = Contact::all();

		return Inertia::render('Admin/Messages', [
			'messages' => $messages,
		]);
	}
}
