<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuestionnaireResponseMail;

class QuestionnaireController extends Controller
{
    public function submitQuestionnaire(Request $request)
    {
        // Get all form data
        $formData = $request->all();

        // Send email
        Mail::to('jmugonyi@zuritconsulting.com')->send(new QuestionnaireResponseMail($formData)); 

        // You can add a success message or redirect here if needed
        return redirect()->back()->with('success', 'Questionnaire submitted successfully! We will be in touch.');
    }
}
