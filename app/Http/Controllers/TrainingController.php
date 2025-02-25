<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use App\Mail\TrainingEnrollment;
use Illuminate\Support\Facades\Mail;

class TrainingController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'training' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|numeric',
        ]);
    
        // Store the data in the database
        Training::create($validatedData);

        Mail::to('info@zuritconsulting.com')->send(new TrainingEnrollment($validatedData));
    
        // Return a response
        return response()->json([
            'message' => 'Enrollment successful!',
        ]);
    }
}
