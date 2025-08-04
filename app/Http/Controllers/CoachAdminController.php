<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Coach;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\CoachAccountMail;
use App\Mail\CoachAssignmentMail;
use App\Mail\CoachDeassignmentMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminCoachAssignmentMail;
use Illuminate\Support\Facades\Storage;
use App\Mail\AdminCoachDeassignmentMail;
use Illuminate\Support\Facades\Validator;

class CoachAdminController extends Controller
{
    /**
     * Display a listing of coaches
     */
    public function index()
    {
        $coaches = Coach::withCount('users')->get();

        return Inertia::render('Admin/Coaching', [
            'coaches' => $coaches,
        ]);
    }

    /**
     * Show the form for creating a new coach
     */
    public function create()
    {
        return Inertia::render('Admin/CoachingCreate');
    }

    /**
     * Store a newly created coach
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:coaches,email',
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        $imagePath = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $imagePath = $photo->move(storage_path('app/public/coaches'), $photoName);
            $data['photo'] = basename($imagePath);
        }

        $existingUser = User::where('email', $data['email'])->first();

        if (!$existingUser) {
            $generatedPassword = Str::random(10);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($generatedPassword),
                'role' => '2'
            ]);

            Mail::to($user->email)->send(new CoachAccountMail($user, $generatedPassword));
        } else {
            Log::info("User with email {$data['email']} already exists. Skipping coach account creation.");
        }

        Coach::create($data);

        return redirect()->route('coaching.index')->with('success', 'Coach created successfully!');
    }

    /**
     * Display the specified coach
     */
    public function show($id)
    {
        $coach = Coach::with('users')->findOrFail($id);

        return Inertia::render('Admin/CoachingShow', [
            'coach' => $coach,
        ]);
    }

    /**
     * Show the form for editing the specified coach
     */
    public function edit($id)
    {
        $coach = Coach::findOrFail($id);

        return Inertia::render('Admin/CoachingEdit', [
            'coach' => $coach,
        ]);
    }

    /**
     * Update the specified coach
     */
    public function update(Request $request, $id)
    {
        $coach = Coach::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:coaches,email,' . $id,
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($coach->photo && Storage::exists(str_replace('storage/', 'public/', $coach->photo))) {
                Storage::delete(str_replace('storage/', 'public/', $coach->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/coaches', $photoName);
            $data['photo'] = 'storage/coaches/' . $photoName;
        }

        $coach->update($data);

        return redirect()->route('coaching.index')->with('success', 'Coach updated successfully!');
    }

    /**
     * Remove the specified coach
     */
    public function destroy($id)
    {
        $coach = Coach::findOrFail($id);

        // Check if coach has assigned users
        if ($coach->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete coach with assigned users. Please reassign users first.');
        }

        // Delete photo if exists
        if ($coach->photo && Storage::exists(str_replace('storage/', 'public/', $coach->photo))) {
            Storage::delete(str_replace('storage/', 'public/', $coach->photo));
        }

        $coach->delete();

        return redirect()->route('coaching.index')->with('success', 'Coach deleted successfully!');
    }

    /**
     * Get coach's clients
     */
    public function getClients($id)
    {
        $coach = Coach::findOrFail($id);
        $clients = $coach->users()->get();

        return response()->json($clients);
    }

    /**
     * Assign user to coach
     */
    public function assignUser(Request $request, $coachId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $coach = Coach::findOrFail($coachId);
        $user = User::findOrFail($request->user_id);

        $user->update(['coach_id' => $coachId]);

        // Send email notification to the user
        try {
            Mail::to($user->email)->send(new CoachAssignmentMail($user, $coach));
        } catch (\Exception $e) {
            // Log the error but don't fail the assignment
            Log::error('Failed to send coach assignment email: ' . $e->getMessage());
        }

        // Send email notification to admin
        try {
            $adminEmail = config('services.email.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new AdminCoachAssignmentMail($user, $coach));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the assignment
            Log::error('Failed to send admin coach assignment email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'User assigned to coach successfully! Email notifications sent.');
    }

    /**
     * Remove user from coach
     */
    public function removeUser(Request $request, $coachId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $coach = Coach::findOrFail($coachId);
        $user = User::findOrFail($request->user_id);

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

        return redirect()->back()->with('success', 'User removed from coach successfully! Email notifications sent.');
    }

    /**
     * Search users for assignment
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json(['users' => []]);
        }

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->with('coach')
            ->limit(10)
            ->get();

        return response()->json(['users' => $users]);
    }
}
