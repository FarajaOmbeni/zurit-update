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
    public function subscribe(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|min:13|max:19',
            'expiry_month' => 'required|numeric|between:1,12',
            'expiry_year' => 'required|numeric|min:0|max:99',
            'cvv' => 'required|numeric|digits_between:3,4',
            'cardholder_name' => 'required|string|max:255',
            'package' => 'required|string|in:monthly,yearly',
        ]);

        $user = auth()->user();
        $name = $user->name;

        // Get package details
        $packages = [
            'monthly' => ['price' => 500, 'duration_days' => 31],
            'yearly' => ['price' => 4500, 'duration_days' => 365]
        ];

        $selectedPackage = $packages[$request->package];
        $amount = $selectedPackage['price'];
        $durationDays = $selectedPackage['duration_days'];

        // Clean card number (remove spaces)
        $cardNumber = str_replace(' ', '', $request->card_number);

        // Basic card validation
        if (!$this->isValidCardNumber($cardNumber)) {
            return back()->withErrors("Invalid card number. Please check your card details.");
        }

        // Check expiry date
        $currentYear = (int) date('y');
        $currentMonth = (int) date('m');

        if (
            (int)$request->expiry_year < $currentYear ||
            ((int)$request->expiry_year == $currentYear && (int)$request->expiry_month < $currentMonth)
        ) {
            return back()->withErrors("Card has expired. Please use a valid card.");
        }

        // Simulate payment processing delay
        sleep(2);

        // For demo purposes, we'll simulate some failures based on card number
        $lastDigit = substr($cardNumber, -1);

        // Simulate 10% failure rate (if last digit is 0)
        if ($lastDigit === '0') {
            return back()->withErrors("Payment declined. Please try a different card or contact your bank.");
        }

        // Create a mock transaction record (you might want to store this in database)
        $transactionId = 'TXN_' . strtoupper(uniqid());
        $maskedCardNumber = $this->maskCardNumber($cardNumber);

        // Log the successful transaction (optional)
        \Log::info('Card payment processed', [
            'user_id' => $user->id,
            'transaction_id' => $transactionId,
            'masked_card' => $maskedCardNumber,
            'amount' => $amount,
            'package' => $request->package,
            'cardholder_name' => $request->cardholder_name,
        ]);

        // Send emails
        try {
            // Email to admin
            Mail::to('ombenifaraja@gmail.com')->send(new SubscriptionMail($name, $maskedCardNumber));
            // Email to user  
            Mail::to($user->email)->send(new UserSubscriptionMail($name, $transactionId));
        } catch (\Exception $e) {
            // Log email error but don't fail the payment
            \Log::error('Failed to send subscription emails', ['error' => $e->getMessage()]);
        }

        // Update user subscription status (add this to your user model or create a subscriptions table)
        $user->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addDays($durationDays),
            'last_payment_date' => now(),
            'subscription_package' => $request->package,
        ]);

        return to_route('budget.index')->with('success', 'Subscription activated successfully!');
    }

    /**
     * Basic Luhn algorithm validation for card numbers
     */
    private function isValidCardNumber($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        $sum = 0;
        $length = strlen($cardNumber);

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int) $cardNumber[$i];

            if (($length - $i) % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        return $sum % 10 === 0;
    }

    /**
     * Mask card number for security
     */
    private function maskCardNumber($cardNumber)
    {
        $length = strlen($cardNumber);
        if ($length < 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4) . substr($cardNumber, -4);
    }

    /**
     * Get card type based on card number
     */
    private function getCardType($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        // Visa
        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        }

        // Mastercard
        if (preg_match('/^5[1-5]/', $cardNumber) || preg_match('/^2[2-7]/', $cardNumber)) {
            return 'Mastercard';
        }

        // American Express
        if (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        }

        // Discover
        if (preg_match('/^6(011|5)/', $cardNumber)) {
            return 'Discover';
        }

        return 'Unknown';
    }

    // public function subscribe(Request $request, MpesaStk $stk) {
    //     $request->validate([
    //         'phone' => 'required'
    //     ]);

    //     $phone = $request->phone;
    //     $user = auth()->user();
    //     $name = $user->name;

    //     $payment  = $stk->sendStkPush(
    //         amount: 1,
    //         phone: $phone,
    //         purpose: 'subscription',
    //         userId: $user->id
    //     );

    //     if (! $stk->waitForConfirmation($payment)) {
    //         return back()->withErrors("Transaction Failed. Please try again.");
    //     }

    //     //Email to admin
    //     Mail::to('ombenifaraja@gmail.com')->send(new SubscriptionMail($name, $phone));
    //     //Email to user
    //     Mail::to('ombenifaraja2000@gmail.com')->send(new UserSubscriptionMail($name));

    //     return to_route('budget.index');
    // }
}
