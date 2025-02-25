<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MarketingMessage;
use App\Models\Marketing_Contact;
use App\Models\TestEmail;

class MarketingMessageController extends Controller
{
    public function index()
    {
        $subscribedUsers = Subscription::all();
        $allUsers = Marketing_Contact::all();

        return view('marketing_admindash', compact('subscribedUsers', 'allUsers'));
    }

    public function sendMessage(Request $request)
    {
        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Initialize users array
        $users = Marketing_Contact::all();

        foreach ($users as $user) {
            // Get the user's email address
            $userEmail = $user->email;

            // Log information about the email sending process
            Log::info("Sending email to user ID $user->id, Email: $userEmail");

            // Send email to the user
            try {
                Mail::to($userEmail)->send(new MarketingMessage(
                    $request->input('title'),
                    $request->input('content'),
                ));
                Log::info("Email sent successfully to user ID $user->id, Email: $userEmail");
            } catch (\Exception $e) {
                Log::error("Error sending email to user ID $user->id, Email: $userEmail. Error: " . $e->getMessage());
            }
        }


        return redirect()->route('marketing_admindash')->with('success', [
            'message' => 'Emails sent successfully!',
            'duration' => 3000,
        ]);

        return redirect()->route('marketing_admindash')->with('error', [
            'message' => 'Error sending Emails!',
            'duration' => 3000,
        ]);
    }
}
