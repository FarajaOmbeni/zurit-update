<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuestionnaireResponseMail extends Mailable
{
    use Queueable, SerializesModels;
    public $formData;

    /**
     * Create a new message instance.
     */
    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Determine subject based on form type
        $subject = match ($this->formData['form_type'] ?? null) {
            'Client Onboarding' => '📋 New Client Onboarding Form Submission',
            'Money Personality Assessment' => '🧠 Money Personality Assessment Response',
            'Risk Tolerance Assessment' => '⚖️ Risk Tolerance Assessment Response',
            'Next Natural Step Assessment' => '🚀 Next Natural Step Assessment Response',
            default => '💰 Money Quiz Response'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.questionnaire-response',
            with: [
                'formData' => $this->formData,
            ],
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
