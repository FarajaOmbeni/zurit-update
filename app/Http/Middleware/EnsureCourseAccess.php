<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\Subcourse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourseAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Determine main course id from route param 'course'
        $param = $request->route('course');
        $courseId = null;

        if ($param instanceof Subcourse) {
            $courseId = $param->course_id;
        } elseif ($param instanceof Course) {
            $courseId = $param->id;
        } else {
            $id = is_numeric($param) ? (int) $param : null;
            if ($id) {
                $sub = Subcourse::find($id);
                if ($sub) {
                    $courseId = $sub->course_id;
                } else {
                    $main = Course::find($id);
                    if ($main) $courseId = $main->id;
                }
            }
        }

        if (!$courseId) {
            // No specific course context; allow through
            return $next($request);
        }

        $hasAccess = DB::table('course_user')
            ->where('course_id', $courseId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$hasAccess) {
            // Remember intended URL and redirect back to courses list to purchase
            session(['elearning.intended_url' => $request->fullUrl()]);
            return redirect()->route('elearning.courses')
                ->with('error', 'Please purchase this course to access it.');
        }

        return $next($request);
    }
}

