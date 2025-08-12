<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Coach;

class AdminCoachDeassignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $coach;
    public $adminName;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Coach $coach, $adminName = 'Admin')
    {
        $this->user = $user;
        $this->coach = $coach;
        $this->adminName = $adminName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Coach Deassignment - ' . $this->user->name . ' removed from ' . $this->coach->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-coach-deassignment',
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
