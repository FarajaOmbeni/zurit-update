<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoachRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;

    /**
     * Create a new message instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($request)
    {
        // If $request is a Request object, get user from it
        $user = $request->user();
        $this->userName = $user ? $user->name : 'Unknown';
        $this->userEmail = $user ? $user->email : 'Unknown';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Coach Request')
            ->view('emails.coach-request')
            ->with([
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
            ]);
    }
}
