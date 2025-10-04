<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Contact;
use App\Models\Testimonial;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminController extends Controller
{
	public function users()
	{
		$users = User::all();

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
