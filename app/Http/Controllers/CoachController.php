<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Coach;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Mail\CoachRequestMail;
use App\Mail\CoachDeassignmentMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        /** @var User $user */
        $user = Auth::user();
        $user->coach_id = $request->coach_id;
        $user->save();

        return redirect()->back()->with('success', 'Coach assigned successfully!');
    }

    public function removeCoach()
    {
        /** @var User $user */
        $user = Auth::user();
        $coach = $user->coach;

        if ($coach) {
            // Store coach info before removing the relationship
            $coachInfo = $coach->toArray();

            $user->coach_id = null;
            $user->save();

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
        // 1. Resolve the coach record (from coaches table) that belongs to this login
        $coach = Coach::where('email', Auth::user()->email)->firstOrFail();

        // 2. Pull the client linked to that coach with all relevant relations
        $client = User::with([
            'incomes',
            'expenses',
            'transactions',
            'goals',
            'investments',
            'debts',
            'assets',
            'liabilities',
        ])
            ->where('id', $clientId)
            ->where('coach_id', $coach->id)
            ->firstOrFail();

        // Prepare lightweight assets/liabilities for charts/tables
        $assetsBasic = $client->assets->map(fn($a) => [
            'name'  => $a->name,
            'value' => (float) $a->value,
        ])->values();

        // Include investments as part of assets for net worth visualizations
        $fixedIncomeTypes = ['mmf', 'bills', 'bonds', 'other'];
        $investmentAssets = $client->investments->map(function ($inv) use ($fixedIncomeTypes) {
            $value = in_array($inv->type, $fixedIncomeTypes, true)
                ? (float) $inv->current_amount
                : (float) $inv->initial_amount;
            return [
                'name'  => $inv->details_of_investment,
                'value' => $value,
            ];
        })->values();

        $assetsBasic = $assetsBasic->concat($investmentAssets)->values();

        // Debts outstanding amount contributes to liabilities for net worth view
        $debtsAsLiabilities = $client->debts->map(fn($d) => [
            'name'   => $d->name,
            'amount' => (float) max(($d->initial_amount - $d->current_amount), 0),
        ])->values();

        $liabilitiesBasic = $client->liabilities->map(fn($l) => [
            'name'   => $l->name,
            'amount' => (float) $l->amount,
        ])->values()->concat($debtsAsLiabilities)->values();

        $totalAssets = $assetsBasic->sum('value');
        $totalLiabilities = $liabilitiesBasic->sum('amount');
        $netWorth = $totalAssets - $totalLiabilities;

        return Inertia::render('Coach/ClientProfile', [
            'client'        => $client,
            // Data buckets for reusing existing UserDashboard components in read-only contexts
            'incomes'       => $client->incomes,
            'expenses'      => $client->expenses,
            'transactions'  => $client->transactions,
            'goals'         => $client->goals,
            'investments'   => $client->investments,
            'debts'         => $client->debts,
            // Simplified assets/liabilities for charts/tables
            'assetsBasic'      => $assetsBasic,
            'liabilitiesBasic' => $liabilitiesBasic,
            'netWorth'      => $netWorth,
        ]);
    }

    // Coach Meetings - for coaches to view all their meetings
    public function meetings()
    {
        $user = Auth::user();

        // Get all meetings for this coach with client information
        $meetings = Meeting::with(['client'])
            ->where('coach_id', $user->id)
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'client_name' => $meeting->client->name,
                    'client_email' => $meeting->client->email,
                    'start_time' => $meeting->start_time,
                    'zoom_id' => $meeting->zoom_id,
                    'join_url' => $meeting->join_url,
                    'start_url' => $meeting->start_url,
                    'status' => $meeting->start_time > now() ? 'upcoming' : 'completed',
                ];
            });

        return Inertia::render('Coach/Meetings', [
            'coach' => $user,
            'meetings' => $meetings,
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

    public function requestCoach(Request $request)
    {
        $user = Auth::user();
        
        Mail::to(config('services.email.admin_email'))->send(new CoachRequestMail($user));

        return redirect()->back()->with('success', 'Coach request sent successfully!');
    }
}
