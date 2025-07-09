<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZuriScoreAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $clientName;
    public $reportDate;
    public $reportMonths;

    /**
     * Create a new message instance.
     */
    public function __construct($clientName, $reportDate, $reportMonths)
    {
        $this->clientName = (string)$clientName;
        $this->reportDate = (string)$reportDate;
        $this->reportMonths = (string)$reportMonths;
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
            view: 'emails.zuriscore-admin',
            with: [
                'clientName' => $this->clientName,
                'reportDate' => $this->reportDate,
                'reportMonths' => $this->reportMonths,
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
