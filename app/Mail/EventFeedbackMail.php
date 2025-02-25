<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $venue, $comprehensiveness, $relevance, $recommendation, $return_client, $value_for_money, $valuable_aspect, $improvement, $suggestion, $improve_experience, $fav_trainor, $testimonial;

    public function __construct($name, $venue, $comprehensiveness, $relevance, $recommendation, $return_client, $value_for_money, $valuable_aspect, $improvement, $suggestion, $improve_experience, $fav_trainor, $testimonial) // changed from $message
    {
        $this->name = (string)$name;
        $this->venue = (string)$venue;
        $this->comprehensiveness = (string)$comprehensiveness;
        $this->relevance = (string)$relevance;
        $this->recommendation = (string)$recommendation;
        $this->return_client = (string)$return_client;
        $this->value_for_money = (string)$value_for_money;
        $this->valuable_aspect = (string)$valuable_aspect;
        $this->improvement = (string)$improvement;
        $this->suggestion = (string)$suggestion;
        $this->improve_experience = (string)$improve_experience;
        $this->fav_trainor = (string)$fav_trainor;
        $this->testimonial = (string)$testimonial;
    }

    public function build()
    {
        return $this->view('emails.feedback-form')
            ->with([
                'name' => $this->name,
                'venue' => $this->venue,
                'comprehensiveness' => $this->comprehensiveness,
                'relevance' => $this->relevance,
                'recommendation' => $this->recommendation,
                'return_client' => $this->return_client,
                'value_for_money' => $this->value_for_money,
                'valuable_aspect' => $this->valuable_aspect,
                'improvement' => $this->improvement,
                'suggestion' => $this->suggestion,
                'improve_experience' => $this->improve_experience,
                'fav_trainor' => $this->fav_trainor,
                'testimonial' => $this->testimonial,
            ])
            ->subject($this->name . ' - Client Feedback Submition');
    }
}
