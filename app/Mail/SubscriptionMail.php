<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $phone;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $phone)
    {
        $this->name = (string)$name;
        $this->phone = (string)$phone;
    }

    public function build()
    {
        return $this->view('emails.subscription-mail')
            ->with([
                'name' => $this->name,
                'email' => $this->phone,
            ])
            ->subject('New Tools Subscription');
    }
}
