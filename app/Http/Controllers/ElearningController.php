<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subcourse;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ElearningController extends Controller
{
    public function landing()
    {
        $courses = Course::with(['subCourses'])->limit(3)->get();

        if (auth()->check()) {
            $userId = auth()->id();
            foreach ($courses as $course) {
                $course->has_access = DB::table('course_user')
                    ->where('course_id', $course->id)
                    ->where('user_id', $userId)
                    ->exists();
            }
        } else {
            foreach ($courses as $course) {
                $course->has_access = false;
            }
        }

        return Inertia::render('Elearning/Landing', [
            'featuredCourses' => $courses,
            'userPhone' => auth()->user()->phone_number ?? '',
        ]);
    }

    public function index()
    {
        $courses = Course::with(['subCourses' => function ($query) {
                $query->orderBy('id', 'asc');
            }, 'subCourses.materials'])
            ->get();

        // Attach access flags for each main course
        if (auth()->check()) {
            $userId = auth()->id();
            foreach ($courses as $mainCourse) {
                $mainCourse->has_access = DB::table('course_user')
                    ->where('course_id', $mainCourse->id)
                    ->where('user_id', $userId)
                    ->exists();
            }
        } else {
            foreach ($courses as $mainCourse) {
                $mainCourse->has_access = false;
            }
        }
        
        // Add quiz completion status for each sub-course
        foreach ($courses as $mainCourse) {
            $totalSubCourses = $mainCourse->subCourses->count();
            $completedSubCourses = 0;
            
            foreach ($mainCourse->subCourses as $index => $subCourse) {
                // Get latest quiz attempt for this sub-course
                $latestAttempt = null;
                if (auth()->check()) {
                    $latestAttempt = QuizAttempt::where('user_id', auth()->id())
                        ->whereHas('quiz', function ($query) use ($subCourse) {
                            $query->where('subcourse_id', $subCourse->id);
                        })
                        ->latest()
                        ->first();
                }
                
                $quizCompleted = (bool) $latestAttempt;
                $quizPassed = $latestAttempt ? $latestAttempt->passed : false;
                
                if ($quizCompleted && $quizPassed) {
                    $completedSubCourses++;
                }
                
                $subCourse->quiz_status = [
                    'completed' => $quizCompleted,
                    'passed' => $quizPassed,
                    'percentage' => $latestAttempt ? $latestAttempt->percentage : 0,
                    'score' => $latestAttempt ? $latestAttempt->score : 0,
                    'total_questions' => $latestAttempt ? $latestAttempt->total_questions : 0,
                ];
            }
            
            // Check if main course is completed (all sub-courses passed)
            $mainCourse->is_completed = ($totalSubCourses > 0 && $completedSubCourses === $totalSubCourses);
            $mainCourse->completion_progress = $totalSubCourses > 0 ? round(($completedSubCourses / $totalSubCourses) * 100, 2) : 0;
        }

        return Inertia::render('Elearning/Courses', [
            'courses' => $courses,
            'userPhone' => auth()->user()->phone_number ?? '',
        ]);
    }

    public function show(Subcourse $course)
    {
        return Inertia::render('Elearning/Course', [
            'course' => $course,
            'materials' => $course->materials

        ]);
    }
}
