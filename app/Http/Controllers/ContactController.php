<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Contact;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request){

        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'userMessage' => 'required',
            'g-recaptcha-response' => 'required',
        ]);
    
        // $url = 'https://recaptchaenterprise.googleapis.com/v1/projects/zuritrecaptcha-1710402514343/assessments?key=AIzaSyCgeBzw4Ek7DBpt9WTuxEuBQ8qfLIZe738';
        $url = '';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',])
        ->post($url, [
            'event' => [
                'token' => $request->input('g-recaptcha-response'),
                'site_key' => env('RECAPTCHA_SECRET'),
            ]
        ]);
    
        $body = $response->json();
       
        // \Log::info('reCAPTCHA response: ', $body);
    
        if ($response->status() == 200) {
    
            $contact = Contact::create($validatedData);
    
            Mail::to('info@zuritconsulting.com')->send(new ContactFormMail(
                $request->input('name'),
                $request->input('email'),
                $request->input('userMessage')
            ));
    
            return redirect()->back()->with('success', 'Message sent successfully!');
        } else {
            
            return redirect()->back()->withErrors(['captcha' => 'ReCaptcha Error']);
        }
    }

    public function showAdminDashboard()
    {
        $contacts = Contact::all();
        return view('contacts_admindash', compact('contacts'));
    }
}
