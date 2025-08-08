<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Meeting;

class ClientMeetingInvite extends Mailable
{
    public function __construct(public Meeting $meeting) {}

    public function build()
    {
        return $this->subject('Your coaching session is booked!')
            ->markdown('emails.meeting_invite');
    }
}
