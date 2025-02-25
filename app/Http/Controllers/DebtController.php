<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index()
    {
        return Inertia::render('UserDashboard/DebtManager');
    }
}
