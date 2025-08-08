<?php

namespace App\Mail;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoachMeetingStartLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Meeting $meeting) {}

    public function build()
    {
        return $this->subject('Start your coaching session')
            ->markdown('emails.coach_meeting_start');
    }
}
