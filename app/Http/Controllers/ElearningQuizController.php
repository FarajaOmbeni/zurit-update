<?php

namespace App\Http\Controllers;

use App\Models\Subcourse;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ElearningQuizController extends Controller
{
    public function show(Subcourse $course)
    {
        $quiz = $course->quizzes()->with(['questions.choices'])->first();
        
        if (!$quiz) {
            return redirect()->route('elearning.course', ['course' => $course->id])
                ->with('error', 'No quiz available for this course.');
        }

        // Check if this quiz is unlocked (user has passed previous quizzes)
        if (!$this->isQuizUnlocked($course)) {
            return redirect()->route('elearning.courses')
                ->with('error', 'You must complete the previous quiz with a passing grade before attempting this one.');
        }

        // Check if quiz has correct answers marked
        $hasCorrectAnswers = true;
        foreach ($quiz->questions as $question) {
            $correctChoice = $question->choices->where('is_correct', true)->first();
            if (!$correctChoice) {
                $hasCorrectAnswers = false;
                break;
            }
        }

        if (!$hasCorrectAnswers) {
            return redirect()->route('elearning.course', ['course' => $course->id])
                ->with('error', 'This quiz is not properly configured. Please contact an administrator.');
        }

        // Check if user has already attempted this quiz
        $latestAttempt = $quiz->attempts()
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        // Format quiz data for frontend
        $quizData = [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'questions' => $quiz->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'choices' => $question->choices->map(function ($choice) {
                        return [
                            'id' => $choice->id,
                            'choice' => $choice->choice
                        ];
                    })
                ];
            })
        ];

        return Inertia::render('Elearning/Quiz', [
            'quiz' => $quizData,
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'parent' => $course->course ? [
                    'id' => $course->course->id,
                    'title' => $course->course->title
                ] : null
            ],
            'latestAttempt' => $latestAttempt ? [
                'score' => $latestAttempt->score,
                'total_questions' => $latestAttempt->total_questions,
                'percentage' => $latestAttempt->percentage,
                'passed' => $latestAttempt->passed,
                'completed_at' => $latestAttempt->created_at
            ] : null
        ]);
    }

    public function submit(Request $request, Subcourse $course)
    {
        $quiz = $course->quizzes()->with(['questions.choices'])->first();
        
        if (!$quiz) {
            return redirect()->route('elearning.course', ['course' => $course->id])
                ->with('error', 'No quiz available for this course.');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer|exists:quiz_choices,id'
        ]);

        $score = 0;
        $totalQuestions = $quiz->questions->count();
        $results = [];

        foreach ($quiz->questions as $question) {
            $userAnswer = $request->answers[$question->id] ?? null;
            $correctChoice = $question->choices->where('is_correct', true)->first();
            

            
            $isCorrect = $userAnswer == ($correctChoice ? $correctChoice->id : null);
            if ($isCorrect) {
                $score++;
            }

            $results[] = [
                'question' => $question->question,
                'user_answer' => $question->choices->find($userAnswer)->choice ?? 'Not answered',
                'correct_answer' => $correctChoice ? $correctChoice->choice : 'No correct answer marked',
                'is_correct' => $isCorrect
            ];
        }

        $percentage = round(($score / $totalQuestions) * 100, 2);
        $passed = $percentage >= 70;

        // Save quiz attempt
        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => $percentage,
            'passed' => $passed,
            'answers' => $request->answers
        ]);

        // Store results in session for the results page
        session([
            'quiz_results' => [
                'score' => $score,
                'totalQuestions' => $totalQuestions,
                'percentage' => $percentage,
                'passed' => (bool) $passed,
                'results' => $results,
                'attempt_id' => $attempt->id
            ]
        ]);

        // Redirect to results page to avoid POST route issues
        return redirect()->route('elearning.quiz.results', ['course' => $course->id]);
    }

    public function results(Subcourse $course)
    {
        // Get results from session or latest attempt
        $sessionResults = session('quiz_results');
        
        if (!$sessionResults) {
            // If no session data, get the latest attempt
            $latestAttempt = QuizAttempt::where('user_id', auth()->id())
                ->whereHas('quiz', function ($query) use ($course) {
                    $query->where('subcourse_id', $course->id);
                })
                ->latest()
                ->first();
            
            if (!$latestAttempt) {
                return redirect()->route('elearning.quiz', ['course' => $course->id])
                    ->with('error', 'No quiz results found.');
            }
            
            // Reconstruct results from the latest attempt
            $quiz = $course->quizzes()->with(['questions.choices'])->first();
            $results = [];
            
            foreach ($quiz->questions as $question) {
                $userAnswerId = $latestAttempt->answers[$question->id] ?? null;
                $userAnswer = $userAnswerId ? $question->choices->find($userAnswerId) : null;
                $correctChoice = $question->choices->where('is_correct', true)->first();
                
                $results[] = [
                    'question' => $question->question,
                    'user_answer' => $userAnswer ? $userAnswer->choice : 'Not answered',
                    'correct_answer' => $correctChoice ? $correctChoice->choice : 'No correct answer marked',
                    'is_correct' => $userAnswerId == ($correctChoice ? $correctChoice->id : null)
                ];
            }
            
            $sessionResults = [
                'score' => $latestAttempt->score,
                'totalQuestions' => $latestAttempt->total_questions,
                'percentage' => $latestAttempt->percentage,
                'passed' => $latestAttempt->passed,
                'results' => $results,
                'attempt_id' => $latestAttempt->id
            ];
        }
        
        // Clear session data after using it
        session()->forget('quiz_results');
        
        return Inertia::render('Elearning/QuizResult', [
            'score' => $sessionResults['score'],
            'totalQuestions' => $sessionResults['totalQuestions'],
            'percentage' => $sessionResults['percentage'],
            'passed' => $sessionResults['passed'],
            'results' => $sessionResults['results'],
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'parent' => $course->course ? [
                    'id' => $course->course->id,
                    'title' => $course->course->title
                ] : null
            ]
        ]);
    }

    private function isQuizUnlocked(Subcourse $course)
    {
        // Unlock by sequence of subcourses ordered by id
        $mainCourse = $course->course;
        if (!$mainCourse) {
            return true; // Safety: treat as unlocked
        }

        $allSubCourses = $mainCourse->subCourses()->orderBy('id')->get();
        $currentCourseIndex = $allSubCourses->search(function ($subCourse) use ($course) {
            return $subCourse->id === $course->id;
        });

        // First sub-course is always unlocked
        if ($currentCourseIndex === 0) {
            return true;
        }

        // Check if the previous sub-course quiz was passed
        $previousSubCourse = $allSubCourses[$currentCourseIndex - 1] ?? null;
        if (!$previousSubCourse) {
            return true;
        }

        $previousAttempt = QuizAttempt::where('user_id', auth()->id())
            ->whereHas('quiz', function ($query) use ($previousSubCourse) {
                $query->where('subcourse_id', $previousSubCourse->id);
            })
            ->latest()
            ->first();

        return $previousAttempt && $previousAttempt->passed;
    }
}
