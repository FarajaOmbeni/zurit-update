<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::all();

        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }

    public function system() {
        $users = User::all()->count();
        $blogs = Blog::all()->count();
        $subscribed = 0;

        return Inertia::render('Admin/System', [
            'users' => $users,
            'blogs' => $blogs,
            'subscribed' => $subscribed,
        ]);
    }

    public function messages() {
        $messages = Contact::all();

        return Inertia::render('Admin/Messages', [
            'messages' => $messages,
        ]);
    }
}
