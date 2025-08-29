<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subcourse;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CourseController extends Controller
{
	public function index()
	{
		$mainCourses = Course::with(['subCourses.materials'])
			->latest()
			->get();

		return Inertia::render('Admin/Courses/Index', [
			'courses' => [
				'data' => $mainCourses,
				'current_page' => 1,
				'last_page' => 1,
				'per_page' => 10,
				'total' => $mainCourses->count()
			]
		]);
	}

	public function create()
	{
		return Inertia::render('Admin/Courses/Create', [
			'mainCourses' => Course::all()
		]);
	}

	public function createMain()
	{
		return Inertia::render('Admin/Courses/CreateMain');
	}

	public function store(Request $request)
	{
		// If parent_id is present, create a Subcourse under the given Course
		if ($request->filled('parent_id')) {
			$validated = $request->validate([
				'title' => 'required|string|max:255',
				'description' => 'required|string',
				'parent_id' => 'required|exists:courses,id',
				'materials' => 'sometimes|array',
				'materials.*.title' => 'required_with:materials|string|max:255',
				'materials.*.file' => 'required_with:materials|file|mimes:pdf|max:10240',
			]);

			$subcourse = Subcourse::create([
				'course_id' => $validated['parent_id'],
				'title' => $validated['title'],
				'description' => $validated['description'],
			]);

			if ($request->has('materials')) {
				foreach ($request->materials as $material) {
					$file = $material['file'];
					$path = $file->store('', 'course_materials');

					CourseMaterial::create([
						'subcourse_id' => $subcourse->id,
						'title' => $material['title'],
						'file_path' => $path,
						'file_name' => $file->getClientOriginalName(),
						'file_size' => $this->formatFileSize($file->getSize())
					]);
				}
			}
		} else {
			// Create a main Course
			$validated = $request->validate([
				'title' => 'required|string|max:255',
			]);

			Course::create([
				'title' => $validated['title']
			]);
		}

		return redirect()->route('admin.courses.index')
			->with('success', 'Course created successfully!');
	}

	public function edit($course)
	{
		$mainCourse = Course::with(['subCourses.materials'])->findOrFail($course);
		return Inertia::render('Admin/Courses/Edit', [
			'course' => $mainCourse,
			'mainCourses' => Course::where('id', '!=', $mainCourse->id)->get()
		]);
	}

	public function editSubcourse($subcourse)
	{
		$sub = Subcourse::with(['materials', 'course'])->findOrFail($subcourse);
		$sub->setAttribute('parent_id', $sub->course_id);
		return Inertia::render('Admin/Courses/Edit', [
			'course' => $sub,
			'mainCourses' => Course::where('id', '!=', $sub->course_id)->get()
		]);
	}

	public function update(Request $request, $course)
	{
		$mainCourse = Course::findOrFail($course);
		$validated = $request->validate([
			'title' => 'required|string|max:255',
		]);
		$mainCourse->update([
			'title' => $validated['title']
		]);

		return redirect()->route('admin.courses.index')
			->with('success', 'Course updated successfully!');
	}

	public function updateSubcourse(Request $request, $subcourse)
	{
		$sub = Subcourse::with('materials')->findOrFail($subcourse);
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'parent_id' => 'nullable|exists:courses,id',
			'materials' => 'sometimes|array',
			'materials.*.title' => 'required_with:materials|string|max:255',
			'materials.*.file' => 'nullable|file|mimes:pdf|max:10240',
			'materials.*.existing_file' => 'sometimes|string',
		]);

		$sub->update([
			'title' => $validated['title'],
			'description' => $validated['description'],
			'course_id' => $validated['parent_id'] ?? $sub->course_id,
		]);

		if ($request->has('materials')) {
			$existingMaterialIds = [];

			foreach ($request->materials as $material) {
				$fileData = [
					'title' => $material['title'],
				];

				if (isset($material['file'])) {
					$file = $material['file'];
					$path = $file->store('', 'course_materials');
					$fileData['file_path'] = $path;
					$fileData['file_name'] = $file->getClientOriginalName();
					$fileData['file_size'] = $this->formatFileSize($file->getSize());
				}

				if (isset($material['id'])) {
					$existingMaterial = CourseMaterial::find($material['id']);
					if ($existingMaterial) {
						$existingMaterial->update($fileData);
						$existingMaterialIds[] = $existingMaterial->id;
					}
				} else {
					$newMaterial = $sub->materials()->create($fileData);
					$existingMaterialIds[] = $newMaterial->id;
				}
			}

			// Delete materials not included in the update
			$sub->materials()->whereNotIn('id', $existingMaterialIds)->delete();
		} else {
			// If no materials are sent, delete all existing ones
			$sub->materials()->delete();
		}

		return redirect()->route('admin.courses.index')
			->with('success', 'Course updated successfully!');
	}

	public function destroy($course)
	{
		// Delete subcourse if exists
		$subcourse = Subcourse::with('materials')->find($course);
		if ($subcourse) {
			$subcourse->materials()->delete();
			$subcourse->delete();
			return redirect()->route('admin.courses.index')
				->with('success', 'Course deleted successfully!');
		}

		// Else delete main course and all its subcourses + materials
		$mainCourse = Course::with('subCourses.materials')->findOrFail($course);
		foreach ($mainCourse->subCourses as $sc) {
			$sc->materials()->delete();
			$sc->delete();
		}
		$mainCourse->delete();

		return redirect()->route('admin.courses.index')
			->with('success', 'Course deleted successfully!');
	}

	private function formatFileSize($bytes)
	{
		if ($bytes >= 1048576) {
			return round($bytes / 1048576, 2) . ' MB';
		} elseif ($bytes >= 1024) {
			return round($bytes / 1024, 2) . ' KB';
		}
		return $bytes . ' bytes';
	}
}