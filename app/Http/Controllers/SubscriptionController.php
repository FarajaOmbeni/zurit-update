<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\PesapalService;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\SubscriptionMail;
use App\Mail\UserSubscriptionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    private $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        $this->pesapalService = $pesapalService;
    }

    public function index()
    {
        $user = Auth::user();

        // Pass subscription status to the view
        return Inertia::render('UserDashboard/Subscription', [
            'subscriptionStatus' => [
                'status' => $user->subscription_status ?? 'inactive',
                'expires_at' => $user->subscription_expires_at,
                'package' => $user->subscription_package ?? null,
                'last_payment_date' => $user->last_payment_date
            ]
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'package' => 'required|string|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $package = $request->package;

        try {
            // Create Pesapal payment order
            $orderResponse = $this->pesapalService->createSubscriptionOrder($user, $package);

            // Log the order response for debugging
            Log::info('Pesapal order response received', [
                'user_id' => $user->id,
                'package' => $package,
                'response' => $orderResponse
            ]);

            if (!$orderResponse) {
                return back()->withErrors('Failed to create payment order. Please try again.');
            }

            // Check for redirect URL (required for payment)
            if (!isset($orderResponse['redirect_url']) || empty($orderResponse['redirect_url'])) {
                Log::error('Pesapal response missing redirect_url', [
                    'response' => $orderResponse
                ]);
                return back()->withErrors('Payment system error. Please try again or contact support.');
            }

            // Store pending subscription info in session
            session(['pending_subscription' => [
                'package' => $package,
                'order_tracking_id' => $orderResponse['order_tracking_id'] ?? null,
                'order_merchant_reference' => $orderResponse['order_merchant_reference'] ?? null,
                'order_id' => $orderResponse['order_id'] ?? null,
            ]]);

            // Use Inertia location for external redirect to Pesapal payment page
            return Inertia::location($orderResponse['redirect_url']);
        } catch (\Exception $e) {
            Log::error('Subscription creation failed', [
                'user_id' => $user->id,
                'package' => $package,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors('Payment processing failed. Please try again or contact support.');
        }
    }

    /**
     * Handle Pesapal callback after payment
     */
    public function handleCallback(Request $request)
    {
        $orderTrackingId = $request->query('OrderTrackingId');
        $orderMerchantReference = $request->query('OrderMerchantReference');

        if (!$orderTrackingId) {
            return redirect()->route('subscription.plans')
                ->withErrors('Invalid payment callback. Please try again.');
        }

        try {
            // Get transaction status from Pesapal
            $status = $this->pesapalService->getTransactionStatus($orderTrackingId);

            if (!$status) {
                return redirect()->route('subscription.plans')
                    ->withErrors('Unable to verify payment status. Please contact support.');
            }

            $paymentStatus = $status['payment_status_description'] ?? '';
            $user = Auth::user();

            if (strtolower($paymentStatus) === 'completed') {
                // Payment successful - activate subscription
                $this->activateSubscription($user, $orderTrackingId, $status);

                return redirect()->route('budget.index')
                    ->with('success', 'Subscription activated successfully! Welcome to Prosperity Tools.');
            } else {
                // Payment failed or pending
                $message = $paymentStatus === 'Failed'
                    ? 'Payment failed. Please try again with a different payment method.'
                    : 'Payment is still processing. Please wait a few minutes and check your subscription status.';

                return redirect()->route('subscription.plans')
                    ->withErrors($message);
            }
        } catch (\Exception $e) {
            Log::error('Subscription callback processing failed', [
                'order_tracking_id' => $orderTrackingId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('subscription.plans')
                ->withErrors('Payment verification failed. Please contact support if payment was deducted.');
        }
    }

    /**
     * Handle Pesapal IPN notifications
     */
    public function handleIpn(Request $request)
    {
        $orderTrackingId = $request->query('OrderTrackingId');
        $orderMerchantReference = $request->query('OrderMerchantReference');

        Log::info('Pesapal IPN received', [
            'order_tracking_id' => $orderTrackingId,
            'order_merchant_reference' => $orderMerchantReference,
            'full_request' => $request->all()
        ]);

        if (!$orderTrackingId) {
            return response('Invalid IPN', 400);
        }

        try {
            // Get transaction status
            $status = $this->pesapalService->getTransactionStatus($orderTrackingId);

            if (!$status) {
                Log::error('Unable to get transaction status for IPN', [
                    'order_tracking_id' => $orderTrackingId
                ]);
                return response('Unable to verify transaction', 400);
            }

            $paymentStatus = $status['payment_status_description'] ?? '';

            if (strtolower($paymentStatus) === 'completed') {
                // Find user by order reference or email from the transaction
                $this->processCompletedPayment($orderTrackingId, $status);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('IPN processing failed', [
                'order_tracking_id' => $orderTrackingId,
                'error' => $e->getMessage()
            ]);
            return response('Processing failed', 500);
        }
    }

    /**
     * Activate user subscription
     */
    private function activateSubscription($user, $orderTrackingId, $paymentData)
    {
        $pendingSubscription = session('pending_subscription');
        $package = $pendingSubscription['package'] ?? 'monthly';

        $packages = [
            'monthly' => ['duration_days' => 31],
            'yearly' => ['duration_days' => 365]
        ];

        $durationDays = $packages[$package]['duration_days'];

        DB::transaction(function () use ($user, $package, $durationDays, $orderTrackingId, $paymentData) {
            // Update user subscription
            $user->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addDays($durationDays),
                'last_payment_date' => now(),
                'subscription_package' => $package,
            ]);

            // Log the successful payment
            Log::info('Subscription activated', [
                'user_id' => $user->id,
                'package' => $package,
                'order_tracking_id' => $orderTrackingId,
                'amount' => $paymentData['amount'] ?? 0,
                'expires_at' => $user->subscription_expires_at
            ]);
        });

        // Clear pending subscription from session
        session()->forget('pending_subscription');

        // Send notification emails
        $this->sendSubscriptionEmails($user, $package, $orderTrackingId);
    }

    /**
     * Process completed payment from IPN
     */
    private function processCompletedPayment($orderTrackingId, $paymentData)
    {
        // This method handles payments completed via IPN (webhooks)
        // You might need to find the user differently here if the session is not available
        Log::info('Processing completed payment from IPN', [
            'order_tracking_id' => $orderTrackingId,
            'payment_data' => $paymentData
        ]);

        // Additional logic for IPN-based subscription activation can be added here
    }

    /**
     * Send subscription confirmation emails
     */
    private function sendSubscriptionEmails($user, $package, $transactionId)
    {
        try {
            // Email to admin
            Mail::to('ombenifaraja@gmail.com')->send(new SubscriptionMail($user->name, $package));

            // Email to user  
            Mail::to($user->email)->send(new UserSubscriptionMail($user->name, $transactionId));
        } catch (\Exception $e) {
            Log::error('Failed to send subscription emails', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $user->update([
            'subscription_status' => 'cancelled',
            // Keep expires_at so user can access until expiry
        ]);

        return redirect()->route('subscription.plans')
            ->with('success', 'Subscription cancelled. You can access tools until your current period expires.');
    }

    /**
     * Reactivate subscription
     */
    public function reactivate(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If subscription hasn't expired yet, just reactivate
        if ($user->subscription_expires_at && $user->subscription_expires_at > now()) {
            $user->update(['subscription_status' => 'active']);

            return redirect()->route('budget.index')
                ->with('success', 'Subscription reactivated successfully!');
        }

        // Otherwise, redirect to new subscription
        return redirect()->route('subscription.plans')
            ->with('info', 'Please select a new subscription plan.');
    }
}
