<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Meeting;
use App\Services\IcsService;

class ClientMeetingInvite extends Mailable
{
    public function __construct(public Meeting $meeting) {}

    public function build()
    {
        $icsService = new IcsService();
        $icsContent = $icsService->generateIcsFile($this->meeting);

        return $this->subject('Your coaching session is booked!')
            ->markdown('emails.meeting_invite')
            ->attachData($icsContent, 'coaching-session.ics', [
                'mime' => 'text/calendar'
            ]);
    }
}
