<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Mail\BookingFormMail;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'booking_datetime' => 'required|date',
            'additional_information' => 'nullable|string',
        ]);

        Booking::create($validatedData);

        // Send email only if the validation and database insertion are successful
        Mail::to(config('services.email.admin_email'))->send(new BookingFormMail(
            $request->input('name'),
            $request->input('email'),
            $request->input('booking_datetime'),
            $request->input('additional_information')
        ));

        // Redirect to the 'training' route or 'training.blade.php' view
        return redirect()->route('training')->with('success', 'Booking submitted successfully!');
    }
}
