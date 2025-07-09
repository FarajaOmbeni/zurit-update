<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Imports\ExcelImport;
use App\Mail\MarketingMessage;
use Illuminate\Http\Request;
use App\Models\Marketing_Contact;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class MarketingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Marketing');
    }

    public function sendEmails(Request $request) {
        $emails = User::all()->pluck('email')->toArray();
        set_time_limit(0);
        $request->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        $subject = $request->subject;
        $content = $request->content;

        foreach ($emails as $email) {
            Mail::to($email)->queue(new MarketingMessage($subject, $content));
        }
        // Mail::to('ombenifaraja2000@gmail.com')->send(new MarketingMessage($subject, $content));

        return to_route('marketing.index');
    }


    public function add_users_view()
    {
        return view('add_users_admindash');
    }

    public function add_users(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
        ]);

        try {
            $user = Marketing_Contact::create($validatedData);
            return redirect('/add_users_view')->with('success', [
                'message' => 'Users Added Successfully',
                'duration' => 3000,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'The email address is already in use.'])->withInput();
        }
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Process the Excel file
        Excel::import(new ExcelImport, $file);

        return redirect('/add_users_view')->with('success', [
            'message' => 'Users Added Successfully',
            'duration' => 3000,
        ]);

    }
}
