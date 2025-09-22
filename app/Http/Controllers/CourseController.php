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
		try {
			// If parent_id is present, create a Subcourse under the given Course
			if ($request->filled('parent_id')) {
				$validated = $request->validate([
					'title' => 'required|string|max:255',
					'description' => 'required|string',
					'parent_id' => 'required|exists:courses,id',
					'materials' => 'sometimes|array',
					'materials.*.title' => 'required_with:materials|string|max:255',
					'materials.*.file' => 'sometimes|file|mimes:pdf|max:10240',
					'materials.*.video_file' => 'sometimes|file|mimes:mp4,webm,ogg,avi,mov|max:204800', // 200MB
				]);

			$subcourse = Subcourse::create([
				'course_id' => $validated['parent_id'],
				'title' => $validated['title'],
				'description' => $validated['description'],
			]);

			if ($request->has('materials')) {
				foreach ($request->materials as $material) {
					$baseMaterialData = [
						'subcourse_id' => $subcourse->id,
						'title' => $material['title'],
					];

					// Create PDF material if file is uploaded
					if (isset($material['file']) && $material['file']) {
						$file = $material['file'];
						$path = $file->store('', 'course_materials');
						
						$pdfMaterialData = array_merge($baseMaterialData, [
							'type' => 'pdf',
							'file_path' => $path,
							'file_name' => $file->getClientOriginalName(),
							'file_size' => $this->formatFileSize($file->getSize()),
							'title' => $material['title'] . ' (PDF)'
						]);

						CourseMaterial::create($pdfMaterialData);
					}

					// Create video material if video is uploaded
					if (isset($material['video_file']) && $material['video_file']) {
						$video = $material['video_file'];
						$videoPath = $video->store('videos', 'course_materials');
						
						$videoMaterialData = array_merge($baseMaterialData, [
							'type' => 'video',
							'file_path' => $videoPath,
							'file_name' => $video->getClientOriginalName(),
							'file_size' => $this->formatFileSize($video->getSize()),
							'title' => $material['title'] . ' (Video)'
						]);

						CourseMaterial::create($videoMaterialData);
					}
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
		
		} catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
			return back()->withErrors(['error' => 'The uploaded file is too large. Please reduce the file size or contact your administrator to increase upload limits.']);
		} catch (\Illuminate\Validation\ValidationException $e) {
			return back()->withErrors($e->errors())->withInput();
		} catch (\Exception $e) {
			return back()->withErrors(['error' => 'An error occurred while creating the course. Please try again.'])->withInput();
		}
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
		try {
			$sub = Subcourse::with('materials')->findOrFail($subcourse);
			$validated = $request->validate([
				'title' => 'required|string|max:255',
				'description' => 'required|string',
				'parent_id' => 'nullable|exists:courses,id',
				'materials' => 'sometimes|array',
				'materials.*.title' => 'required_with:materials|string|max:255',
				'materials.*.file' => 'nullable|file|mimes:pdf|max:10240',
				'materials.*.video_file' => 'sometimes|file|mimes:mp4,webm,ogg,avi,mov|max:204800', // 200MB
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
				$baseMaterialData = [
					'title' => $material['title'],
				];

				// Handle existing material updates - but we need to check the type
				if (isset($material['id'])) {
					$existingMaterial = CourseMaterial::find($material['id']);
					if ($existingMaterial) {
						// Update existing material (keep same type)
						$updateData = $baseMaterialData;
						
						if ($existingMaterial->type === 'pdf' && isset($material['file'])) {
							// Update PDF
							$file = $material['file'];
							$path = $file->store('', 'course_materials');
							$updateData['file_path'] = $path;
							$updateData['file_name'] = $file->getClientOriginalName();
							$updateData['file_size'] = $this->formatFileSize($file->getSize());
							$updateData['type'] = 'pdf';
						} elseif ($existingMaterial->type === 'video' && isset($material['video_file'])) {
							// Update Video
							$video = $material['video_file'];
							$videoPath = $video->store('videos', 'course_materials');
							$updateData['file_path'] = $videoPath;
							$updateData['file_name'] = $video->getClientOriginalName();
							$updateData['file_size'] = $this->formatFileSize($video->getSize());
							$updateData['type'] = 'video';
						}
						
						$existingMaterial->update($updateData);
						$existingMaterialIds[] = $existingMaterial->id;
						
						// Handle adding new file types to existing material base title
						$baseTitle = str_replace([' (PDF)', ' (Video)'], '', $material['title']);
						
						// If this is a PDF material and user uploads video, create new video material
						if ($existingMaterial->type === 'pdf' && isset($material['video_file']) && $material['video_file']) {
							$video = $material['video_file'];
							$videoPath = $video->store('videos', 'course_materials');
							
							$videoMaterialData = [
								'subcourse_id' => $sub->id,
								'title' => $baseTitle . ' (Video)',
								'type' => 'video',
								'file_path' => $videoPath,
								'file_name' => $video->getClientOriginalName(),
								'file_size' => $this->formatFileSize($video->getSize()),
							];

							$newVideoMaterial = $sub->materials()->create($videoMaterialData);
							$existingMaterialIds[] = $newVideoMaterial->id;
						}
						
						// If this is a Video material and user uploads PDF, create new PDF material
						if ($existingMaterial->type === 'video' && isset($material['file']) && $material['file']) {
							$file = $material['file'];
							$path = $file->store('', 'course_materials');
							
							$pdfMaterialData = [
								'subcourse_id' => $sub->id,
								'title' => $baseTitle . ' (PDF)',
								'type' => 'pdf',
								'file_path' => $path,
								'file_name' => $file->getClientOriginalName(),
								'file_size' => $this->formatFileSize($file->getSize()),
							];

							$newPdfMaterial = $sub->materials()->create($pdfMaterialData);
							$existingMaterialIds[] = $newPdfMaterial->id;
						}
					}
				} else {
					// Create new materials (PDF and/or Video)
					if (isset($material['file']) && $material['file']) {
						$file = $material['file'];
						$path = $file->store('', 'course_materials');
						
						$pdfMaterialData = array_merge($baseMaterialData, [
							'type' => 'pdf',
							'file_path' => $path,
							'file_name' => $file->getClientOriginalName(),
							'file_size' => $this->formatFileSize($file->getSize()),
							'title' => $material['title'] . ' (PDF)'
						]);

						$newMaterial = $sub->materials()->create($pdfMaterialData);
						$existingMaterialIds[] = $newMaterial->id;
					}

					if (isset($material['video_file']) && $material['video_file']) {
						$video = $material['video_file'];
						$videoPath = $video->store('videos', 'course_materials');
						
						$videoMaterialData = array_merge($baseMaterialData, [
							'type' => 'video',
							'file_path' => $videoPath,
							'file_name' => $video->getClientOriginalName(),
							'file_size' => $this->formatFileSize($video->getSize()),
							'title' => $material['title'] . ' (Video)'
						]);

						$newMaterial = $sub->materials()->create($videoMaterialData);
						$existingMaterialIds[] = $newMaterial->id;
					}
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
		
		} catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
			return back()->withErrors(['error' => 'The uploaded file is too large. Please reduce the file size or contact your administrator to increase upload limits.']);
		} catch (\Illuminate\Validation\ValidationException $e) {
			return back()->withErrors($e->errors())->withInput();
		} catch (\Exception $e) {
			return back()->withErrors(['error' => 'An error occurred while updating the course. Please try again.'])->withInput();
		}
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

	public function serveVideo($materialId)
	{
		$material = CourseMaterial::findOrFail($materialId);
		
		// Ensure this is a video material
		if ($material->type !== 'video' || !$material->file_path) {
			abort(404);
		}

		$filePath = storage_path('app/course_materials/' . $material->file_path);
		
		if (!file_exists($filePath)) {
			abort(404);
		}

		return response()->file($filePath, [
			'Content-Type' => 'video/mp4',
			'Content-Disposition' => 'inline; filename="' . $material->file_name . '"'
		]);
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