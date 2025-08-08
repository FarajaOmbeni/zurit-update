<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Services\ZoomService;
use App\Mail\ClientMeetingInvite;
use App\Mail\CoachMeetingStartLink;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class CreateMeetingController extends Controller
{
    public function __invoke(Request $request, ZoomService $zoom)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'date'      => 'required|date',
            'time'      => 'required',
        ]);

        $start = Carbon::parse($request->date . ' ' . $request->time, Auth::user()->timezone ?? 'UTC')
            ->toIso8601String();

        $zoomPayload = [
            'topic'      => 'Coaching Session',
            'type'       => 2,   // scheduled
            'start_time' => $start,
            'timezone'   => Auth::user()->timezone ?? 'UTC',
            'duration'   => 60,
            'settings'   => [
                'join_before_host' => true,
                'waiting_room'     => false,
            ],
        ];

        $zoomMeeting = $zoom->createMeeting($zoomPayload);

        // Persist in DB (simplified)
        $meeting = Meeting::create([
            'coach_id'     => Auth::id(),
            'client_id'    => $request->client_id,
            'zoom_id'      => $zoomMeeting['id'],
            'join_url'     => $zoomMeeting['join_url'],
            'start_url'    => $zoomMeeting['start_url'],
            'start_time'   => $start,
        ]);

        // Dispatch email
        Mail::to($meeting->client->email)
            ->queue(new ClientMeetingInvite($meeting));

        // Email coach with start URL
        Mail::to($meeting->coach->email)
            ->queue(new CoachMeetingStartLink($meeting));

        return to_route('coach.dashboard');
    }
}
