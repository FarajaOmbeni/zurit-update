<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CoachController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coach = $user->coach;
        $availableCoaches = Coach::all();

        return Inertia::render('UserDashboard/Coach', [
            'user' => $user,
            'coach' => $coach,
            'availableCoaches' => $availableCoaches,
        ]);
    }

    public function assignCoach(Request $request)
    {
        $request->validate([
            'coach_id' => 'required|exists:coaches,id',
        ]);

        $user = Auth::user();
        $user->update(['coach_id' => $request->coach_id]);

        return redirect()->back()->with('success', 'Coach assigned successfully!');
    }

    public function removeCoach()
    {
        $user = Auth::user();
        $user->update(['coach_id' => null]);

        return redirect()->back()->with('success', 'Coach removed successfully!');
    }

    // Coach Dashboard - for coaches to view their clients
    public function dashboard()
    {
        $coach = Auth::user();

        // Get all clients assigned to this coach
        $clients = User::where('coach_id', $coach->id)
            ->withCount(['goals', 'investments'])
            ->get();

        return Inertia::render('Coach/Home', [
            'coach' => $coach,
            'clients' => $clients,
        ]);
    }

    // View specific client profile
    public function viewClient($clientId)
    {
        $coach = Auth::user();
        $client = User::where('id', $clientId)
            ->where('coach_id', $coach->id)
            ->with(['goals', 'investments', 'debts', 'transactions'])
            ->firstOrFail();

        return Inertia::render('Coach/ClientProfile', [
            'coach' => $coach,
            'client' => $client,
        ]);
    }

    // Get coach's client list for API
    public function getClients()
    {
        $coach = Auth::user();
        $clients = User::where('coach_id', $coach->id)
            ->withCount(['goals', 'investments'])
            ->get();

        return response()->json($clients);
    }
}
