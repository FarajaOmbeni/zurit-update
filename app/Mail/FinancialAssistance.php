<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinancialAssistance extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $type;

    public function __construct($user, $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'help'
            ? 'Urgent: User Needs Financial Assistance'
            : 'Portfolio Optimization Request';

        return $this->view('emails.financial-assistance')
            ->subject($subject)
            ->with([
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'requestType' => $this->type
            ]);
    }
}
