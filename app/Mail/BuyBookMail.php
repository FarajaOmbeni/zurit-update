<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyBookMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $book_title;
    public $phone;
    public $address;

    public function __construct($name, $email, $book_title, $phone, $address)
    {
        $this->name = (string)$name;
        $this->email = (string)$email;
        $this->book_title = (string)$book_title;
        $this->phone = (string)$phone;
        $this->address = (string)$address;
    }

    public function build()
    {
        return $this->view('emails.buy-book-mail')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'book_title' => $this->book_title,
                'phone' => $this->phone,
                'address' => $this->address,
            ])
            ->subject('New Book Order');
    }
}
