<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function generate(Course $course)
    {
        // Verify user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Verify this is a main course (parent_id is null)
        if ($course->parent_id !== null) {
            return redirect()->back()->with('error', 'Certificates can only be generated for main courses.');
        }
        
        // Get all sub-courses for this main course
        $subCourses = $course->subCourses;
        
        if ($subCourses->isEmpty()) {
            return redirect()->back()->with('error', 'This course has no sub-courses.');
        }
        
        // Check if user has passed all quizzes in this main course
        $totalSubCourses = $subCourses->count();
        $passedSubCourses = 0;
        
        foreach ($subCourses as $subCourse) {
            $latestAttempt = QuizAttempt::where('user_id', $user->id)
                ->whereHas('quiz', function ($query) use ($subCourse) {
                    $query->where('course_id', $subCourse->id);
                })
                ->latest()
                ->first();
            
            if ($latestAttempt && $latestAttempt->passed) {
                $passedSubCourses++;
            }
        }
        
        // Verify user has completed all sub-courses
        if ($passedSubCourses !== $totalSubCourses) {
            return redirect()->back()->with('error', 'You must pass all quizzes in this course to generate a certificate.');
        }
        
        // Calculate overall course score (average of all quiz scores)
        $totalScore = 0;
        $quizCount = 0;
        
        foreach ($subCourses as $subCourse) {
            $latestAttempt = QuizAttempt::where('user_id', $user->id)
                ->whereHas('quiz', function ($query) use ($subCourse) {
                    $query->where('course_id', $subCourse->id);
                })
                ->latest()
                ->first();
            
            if ($latestAttempt) {
                $totalScore += $latestAttempt->percentage;
                $quizCount++;
            }
        }
        
        $overallScore = $quizCount > 0 ? round($totalScore / $quizCount, 2) : 0;
        
        // Generate certificate data
        $certificateData = [
            'user_name' => $user->name,
            'course_title' => $course->title,
            'completion_date' => now()->format('F j, Y'),
            'overall_score' => $overallScore,
            'total_subcourses' => $totalSubCourses,
            'certificate_id' => 'CERT-' . strtoupper(substr(md5($user->id . $course->id . now()->timestamp), 0, 8)),
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('certificates.course-completion', $certificateData);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Certificate_' . str_replace(' ', '_', $course->title) . '_' . str_replace(' ', '_', $user->name) . '.pdf';
        
        return $pdf->download($filename);
    }
} 