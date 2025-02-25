<?php
// app/Mail/MarketingMessage.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MarketingMessage as MarketingMessageModel;

class MarketingMessage extends Mailable
{
    use Queueable, SerializesModels;

    //public $marketingMessage;
    public $title;
    public $content;

    public function __construct($title, $content)
    {
        //$this->marketingMessage = $marketingMessage;
        $this->title = $title;
        $this->content = $content;
    }

    public function build()
    {
        return $this->view('emails.marketing_message')
            ->with([
                'title' => $this->title,
                'content' => $this->content,
            ])
            ->subject($this->title);
    }
}
