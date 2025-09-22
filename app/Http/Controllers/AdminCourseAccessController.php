<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\ElearningAccessGrantedMail;

class AdminCourseAccessController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $courses = Course::select('id', 'title', 'price')->orderBy('title')->get();

        $access = DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->select(
                'course_user.id',
                'course_user.user_id',
                'course_user.course_id',
                'course_user.created_at',
                'users.name as user_name',
                'users.email as user_email',
                'courses.title as course_title'
            )
            ->orderByDesc('course_user.created_at')
            ->get();

        return Inertia::render('Admin/Courses/Access', [
            'users' => $users,
            'courses' => $courses,
            'access' => $access,
        ]);
    }

    public function grant(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        DB::table('course_user')->updateOrInsert(
            ['user_id' => $data['user_id'], 'course_id' => $data['course_id']],
            ['updated_at' => now(), 'created_at' => now()]
        );

        // Send email to user notifying access grant
        $user = User::find($data['user_id']);
        $course = Course::find($data['course_id']);
        if ($user && $user->email && $course) {
            Mail::to($user->email)->send(new ElearningAccessGrantedMail($user->name, $course->title));
        }

        return back()->with('success', 'Access granted');
    }

    public function revoke($id)
    {
        DB::table('course_user')->where('id', $id)->delete();
        return back()->with('success', 'Access revoked');
    }
}
