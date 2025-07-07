<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\EventsFeedback;
use App\Mail\EventFeedbackMail;
use Illuminate\Support\Facades\Mail;

class EventsController extends Controller
{
    public function index()
    {
        $events = Event::all();

        return Inertia::render('Admin/Events', ['events' => $events]);
    }

    public function store(Request $request)
    {
        // dd("Validation passed");

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'registration_link' => 'required|url',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->move(storage_path('app/public/events'), $imageName);
        }

        $event = new Event();
        $event->name = $request->name;
        $event->date = $request->date;
        $event->price = $request->price;
        $event->price = $request->price;
        $event->image = basename($imagePath);
        $event->registration_link = $request->registration_link;

        $event->save();

        return to_route('events.index');
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->move(storage_path('app/public/events'), $imageName);
        }

        //validate the inputs
        $request->validate([
            'name' => 'required',
            'date' => 'required',
            'price' => 'required',
            'registration_link' => 'required|url',
        ]);

        $event->name = $request->name;
        $event->date = $request->date;
        $event->registration_link = $request->registration_link;
        $event->price = $request->price;
        $event->image = basename($imagePath);

        $event->update();

        return to_route('events.index');
    }

    public function eventFeedback(Request $request)
    {
        $request->validate([
            'userName' => 'string|max:255',
            'eventName' => 'required|string|max:255',
            'logisticsRating' => 'required|string|max:255',
            'clarityRating' => 'required|string|max:255',
            'relevanceRating' => 'required|string|max:255',
            'recommendationLikelihood' => 'required|string|max:255',
            'attendanceLikelihood' => 'required|string|max:255',
            'valueForMoney' => 'required|string|max:255',
            'mostValuable' => 'required|string|max:255',
            'areaOfImprovement' => 'required|string|max:255',
            'topicSuggestion' => 'required|string|max:255',
            'favoriteSpeaker' => 'required|string|max:255',
        ]);

        $userName = $request->userName;
        $eventName = $request->eventName;
        $logisticsRating = $request->logisticsRating;
        $clarityRating = $request->clarityRating;
        $relevanceRating = $request->relevanceRating;
        $recommendationLikelihood = $request->recommendationLikelihood;
        $attendanceLikelihood = $request->attendanceLikelihood;
        $valueForMoney = $request->valueForMoney;
        $mostValuable = $request->mostValuable;
        $areaOfImprovement = $request->areaOfImprovement;
        $topicSuggestion = $request->topicSuggestion;
        $favoriteSpeaker = $request->favoriteSpeaker;

        // Send email
        if (!isset($request->website)) {
            Mail::to('jmugonyi@zuritconsulting.com')->send(new EventFeedbackMail($userName, $eventName, $logisticsRating, $clarityRating, $relevanceRating, $recommendationLikelihood, $attendanceLikelihood, $valueForMoney, $mostValuable, $areaOfImprovement, $topicSuggestion, $favoriteSpeaker));
        }

        // You can add a success message or redirect here if needed
        return redirect()->back()->with('success', 'Feedback submitted successfully! Thank you for your feedback!');
    }

    public function destroy($event)
    {
        $event = Event::find($event);
        $event->delete();
        
        return to_route('events.index');
    }
}
