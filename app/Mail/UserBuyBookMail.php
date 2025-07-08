<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserBuyBookMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $book_title;
    public $phone;

    public function __construct($name, $email, $book_title, $phone)
    {
        $this->name = (string)$name;
        $this->email = (string)$email;
        $this->book_title = (string)$book_title;
        $this->phone = (string)$phone;
    }

    public function build()
    {
        return $this->view('emails.user-buy-book-mail')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'book_title' => $this->book_title,
                'phone' => $this->phone,
            ])
            ->subject('Order Confirmed');
    }
}
