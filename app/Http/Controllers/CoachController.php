<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CoachDeassignmentMail;
use App\Mail\AdminCoachDeassignmentMail;

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
        $coach = $user->coach;

        if ($coach) {
            // Store coach info before removing the relationship
            $coachInfo = $coach->toArray();

            $user->update(['coach_id' => null]);

            // Send email notification to the user
            try {
                Mail::to($user->email)->send(new CoachDeassignmentMail($user, $coach));
            } catch (\Exception $e) {
                // Log the error but don't fail the deassignment
                Log::error('Failed to send coach deassignment email: ' . $e->getMessage());
            }

            // Send email notification to admin
            try {
                $adminEmail = config('services.email.admin_email');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new AdminCoachDeassignmentMail($user, $coach));
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the deassignment
                Log::error('Failed to send admin coach deassignment email: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Coach removed successfully! Email notifications sent.');
        }

        return redirect()->back()->with('error', 'No coach assigned to remove.');
    }

    // Coach Dashboard - for coaches to view their clients
    public function dashboard()
    {
        $user = Auth::user();

        $coach = Coach::where('email', $user->email)->get();

        $coachId = $coach->first()->id;

        // Get all clients assigned to this coach
        $clients = User::where('coach_id', $coachId)
            ->get();

        return Inertia::render('Coach/Home', [
            'coach' => $user,
            'clients' => $clients,
        ]);
    }

    // View specific client profile
    public function viewClient($clientId)
    {
        $coach = Auth::user();
        $client = User::where('id', $clientId)
            ->where('coach_id', $coach->id)
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
            ->get();

        return response()->json($clients);
    }
}
