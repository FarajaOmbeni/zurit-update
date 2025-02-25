<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $userMessage; 

    public function __construct($name, $email, $userMessage) 
    {
        $this->name = (string)$name;
        $this->email = (string)$email;
        $this->userMessage = (string)$userMessage; 
    }

    public function build()
    {
        return $this->view('emails.contact-form')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'userMessage' => $this->userMessage, 
            ])
            ->subject('New Contact Form Submission');
    }
}