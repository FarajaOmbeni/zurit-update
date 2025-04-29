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
            $absolutePath = '/home/zuriuhqx/public_html/storage/events';
            $image->move($absolutePath, $imageName);
            $imagePath = '/storage/events/' . $imageName;
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

    public function edit($id)
    {
        $event = Event::find($id);

        return view('events_edit_admindash', ['event' => $event]);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $imageName = ''; // Initialize image name variable

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move('/home/zuriuhqx/public_html/events_res/img', $imageName);
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
        $event->image = $imageName;

        $event->save();

        return redirect('/events_admindash')->with('success', [
            'message' => 'Event Updated Successfully!',
            'duration' => 3000,
        ]);
    }

    public function eventFeedback(Request $request)
    {
        //Store to teh Database
        EventsFeedback::create([
            'name' => $request->input('name'),
            'venue' => $request->input('venue'),
            'comprehensiveness' => $request->input('comprehensiveness'),
            'relevance' => $request->input('relevance'),
            'recommendation' => $request->input('recommendation'),
            'return_client' => $request->input('return_client'),
            'value_for_money' => $request->input('value_for_money'),
            'valuable_aspect' => $request->input('valuable_aspect'),
            'improvement' => $request->input('improvement'),
            'suggestion' => $request->input('suggestion'),
            'improve_experience' => $request->input('improve_experience'),
            'fav_trainor' => $request->input('fav_trainor'),
            'testimonial' => $request->input('testimonial'),
        ]);


        //Send email
        Mail::to('info@zuritconsulting.com')->send(new EventFeedbackMail(
            $request->input('name'),
            $request->input('venue'),
            $request->input('comprehensiveness'),
            $request->input('relevance'),
            $request->input('recommendation'),
            $request->input('return_client'),
            $request->input('value_for_money'),
            $request->input('valuable_aspect'),
            $request->input('improvement'),
            $request->input('suggestion'),
            $request->input('improve_experience'),
            $request->input('fav_trainor'),
            $request->input('testimonial'),

        ));

        return redirect('/contactus')->with('success', [
            'message' => 'Feedback Sent Successfully!',
            'duration' => 3000,
        ]);
    }

    public function destroy($event)
    {
        $event = Event::find($event);
        $event->delete();
        return redirect()->route('events_admindash')->with('success', [
            'message' => 'Event Deleted Successfully!',
            'duration' => 3000,
        ]);
    }
}
