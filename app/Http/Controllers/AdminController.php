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

	public function system() {
		$users = User::count();
		$blogs = Blog::count();

		// Subscription overall statistics (mirrors SubscriptionMonitor)
		$totalSubscriptions = Subscription::count();
		$activeSubscriptions = Subscription::active()->count();
		$monthlyRevenue = Subscription::where('status', 'active')
			->where('package', 'monthly')
			->sum('amount');
		$yearlyRevenue = Subscription::where('status', 'active')
			->where('package', 'yearly')
			->sum('amount');
		$mrrEquivalent = $monthlyRevenue + ($yearlyRevenue / 12);

		return Inertia::render('Admin/System', [
			'users' => $users,
			'blogs' => $blogs,
			// For backward compatibility; consider "Subscribed" as active subscriptions
			'subscribed' => $activeSubscriptions,
			// New stats
			'totalSubscriptions' => $totalSubscriptions,
			'activeSubscriptions' => $activeSubscriptions,
			'monthlyRevenue' => (float) $monthlyRevenue,
			'yearlyRevenue' => (float) $yearlyRevenue,
			'mrrEquivalent' => (float) $mrrEquivalent,
		]);
	}

	public function messages() {
		$messages = Contact::all();

		return Inertia::render('Admin/Messages', [
			'messages' => $messages,
		]);
	}
}
