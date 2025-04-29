<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZuriScoreReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $reportUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $reportUrl)
    {
        $this->name = (string)$name;
        $this->reportUrl = (string)$reportUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ZuriScore Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.zuriscore-report',
            with: [
                'name' => $this->name,
                'reportUrl' => $this->reportUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
