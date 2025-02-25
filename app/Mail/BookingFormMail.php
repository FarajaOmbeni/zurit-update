<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $booking_datetime;
    public $additional_information;

    public function __construct($name, $email, $booking_datetime, $additional_information) 
    {
        $this->name = (string)$name;
        $this->email = (string)$email;
        $this->booking_datetime = (string)$booking_datetime;
        $this->additional_information = (string)$additional_information;
    }

    public function build()
    {
        return $this->view('emails.booking-form')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'booking_datetime' => $this->booking_datetime,
                'additional_information' => $this->additional_information,
            ])
            ->subject('New Booking Form Submission');
    }
}