<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eventName, $logisticsRating, $clarityRating, $relevanceRating, $recommendationLikelihood, $attendanceLikelihood, $valueForMoney, $mostValuable, $areaOfImprovement, $topicSuggestion, $favoriteSpeaker;

    public function __construct($eventName, $logisticsRating, $clarityRating, $relevanceRating, $recommendationLikelihood, $attendanceLikelihood, $valueForMoney, $mostValuable, $areaOfImprovement, $topicSuggestion, $favoriteSpeaker)
    {
        $this->eventName = (string)$eventName;
        $this->logisticsRating = (string)$logisticsRating;
        $this->clarityRating = (string)$clarityRating;
        $this->relevanceRating = (string)$relevanceRating;
        $this->recommendationLikelihood = (string)$recommendationLikelihood;
        $this->attendanceLikelihood = (string)$attendanceLikelihood;
        $this->valueForMoney = (string)$valueForMoney;
        $this->mostValuable = (string)$mostValuable;
        $this->areaOfImprovement = (string)$areaOfImprovement;
        $this->topicSuggestion = (string)$topicSuggestion;
        $this->favoriteSpeaker = (string)$favoriteSpeaker;
    }

    public function build()
    {
        return $this->view('emails.feedback-form')
            ->with([
                'eventName' => $this->eventName,
                'logisticsRating' => $this->logisticsRating,
                'clarityRating' => $this->clarityRating,
                'relevanceRating' => $this->relevanceRating,
                'recommendationLikelihood' => $this->recommendationLikelihood,
                'attendanceLikelihood' => $this->attendanceLikelihood,
                'valueForMoney' => $this->valueForMoney,
                'mostValuable' => $this->mostValuable,
                'areaOfImprovement' => $this->areaOfImprovement,
                'topicSuggestion' => $this->topicSuggestion,
                'favoriteSpeaker' => $this->favoriteSpeaker,
            ])
            ->subject($this->eventName . ' - Client Feedback Submission');
    }
}
