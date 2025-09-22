<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ElearningAccessGrantedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $courseTitle;

    public function __construct(string $name, string $courseTitle)
    {
        $this->name = $name;
        $this->courseTitle = $courseTitle;
    }

    public function build()
    {
        return $this->view('emails.elearning-access-granted')
            ->with([
                'name' => $this->name,
                'courseTitle' => $this->courseTitle,
            ])
            ->subject('Course Access Granted');
    }
}

