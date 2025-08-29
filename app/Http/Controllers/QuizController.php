<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subcourse;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizChoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
	public function create()
	{
		$subCourses = Subcourse::with('course')
			->get()
			->map(function ($sub) {
				return [
					'id' => $sub->id,
					'title' => $sub->title,
					'parent_title' => $sub->course?->title,
					'full_title' => ($sub->course?->title ?? 'Course') . ' - ' . $sub->title
				];
			});

		return Inertia::render('Admin/Quizzes/Create', [
			'subCourses' => $subCourses
		]);
	}

	public function store(Request $request)
	{
		$request->validate([
			'subcourse_id' => 'required|exists:subcourses,id',
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'questions' => 'required|array|min:1',
			'questions.*.question' => 'required|string',
			'questions.*.choices' => 'required|array|min:2',
			'questions.*.choices.*.choice' => 'required|string',
			'questions.*.correct_choice' => 'required|integer|min:0'
		]);

		$quiz = Quiz::create([
			'subcourse_id' => $request->subcourse_id,
			'title' => $request->title,
			'description' => $request->description
		]);

		foreach ($request->questions as $questionData) {
			$question = QuizQuestion::create([
				'quiz_id' => $quiz->id,
				'question' => $questionData['question']
			]);

			foreach ($questionData['choices'] as $index => $choiceData) {
				QuizChoice::create([
					'quiz_question_id' => $question->id,
					'choice' => $choiceData['choice'],
					'is_correct' => $index == $questionData['correct_choice']
				]);
			}
		}

		return redirect()->route('admin.quizzes.index')
			->with('success', 'Quiz created successfully!');
	}

	public function index()
	{
		$quizzes = Quiz::with(['subcourse.course'])
			->get()
			->map(function ($quiz) {
				return [
					'id' => $quiz->id,
					'title' => $quiz->title,
					'description' => $quiz->description,
					'course_title' => $quiz->subcourse?->title,
					'parent_course_title' => $quiz->subcourse?->course?->title,
					'questions_count' => $quiz->questions()->count(),
					'created_at' => $quiz->created_at
				];
			});

		return Inertia::render('Admin/Quizzes/Index', [
			'quizzes' => $quizzes
		]);
	}

	public function destroy(Quiz $quiz)
	{
		$quiz->delete();
		return redirect()->route('admin.quizzes.index')
			->with('success', 'Quiz deleted successfully!');
	}
}
