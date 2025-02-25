<?php

namespace App\Http\Controllers;

use App\Imports\ExcelImport;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Marketing_Contact;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class MarketingController extends Controller
{
    public function index()
    {
        $contacts = Marketing_Contact::paginate(10);

        return view('upload_contacts', ['contacts' => $contacts]);
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
