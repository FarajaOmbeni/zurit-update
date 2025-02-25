<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class NetworthController extends Controller
{
    public function index()
    {
        return Inertia::render('UserDashboard/NetworthCalculator');
    }
}
