<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $mainCourses = Course::whereNull('parent_id')
            ->with(['subCourses.materials'])
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
            'mainCourses' => Course::whereNull('parent_id')->get()
        ]);
    }

    public function createMain()
    {
        return Inertia::render('Admin/Courses/CreateMain');
    }

    public function store(Request $request)
    {
        // Check if this is a main course (no parent_id) or sub-course (has parent_id)
        if ($request->has('parent_id') && $request->parent_id) {
            // This is a sub-course - validate description and materials
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'parent_id' => 'required|exists:courses,id',
            'order' => 'required|integer|min:1',
            'materials' => 'sometimes|array',
            'materials.*.title' => 'required_with:materials|string|max:255',
            'materials.*.file' => 'required_with:materials|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $course = Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
            'order' => $validated['order']
        ]);

        if ($request->has('materials')) {
            foreach ($request->materials as $material) {
                $file = $material['file'];
                $path = $file->store('', 'course_materials');

                CourseMaterial::create([
                    'course_id' => $course->id,
                    'title' => $material['title'],
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $this->formatFileSize($file->getSize())
                ]);
            }
            }
        } else {
            // This is a main course - only validate title
            $validated = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $course = Course::create([
                'title' => $validated['title']
            ]);
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function edit(Course $course)
    {
        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course->load(['materials', 'parent']),
            'mainCourses' => Course::whereNull('parent_id')->where('id', '!=', $course->id)->get()
        ]);
    }

    public function update(Request $request, Course $course)
    {
        // Check if this is a main course (no parent_id) or sub-course (has parent_id)
        if ($course->parent_id) {
            // This is a sub-course - validate description and materials
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
                'parent_id' => 'nullable|exists:courses,id',
            'materials' => 'sometimes|array',
            'materials.*.title' => 'required_with:materials|string|max:255',
                'materials.*.file' => 'nullable|file|mimes:pdf|max:10240',
            'materials.*.existing_file' => 'sometimes|string',
        ]);

        $course->update([
            'title' => $validated['title'],
                'description' => $validated['description'],
                'parent_id' => $validated['parent_id'] ?? null
        ]);

        if ($request->has('materials')) {
            $existingMaterialIds = [];
            
            foreach ($request->materials as $material) {
                $fileData = [
                    'title' => $material['title'],
                ];

                if (isset($material['file'])) {
                    // Store new file
                    $file = $material['file'];
                    $path = $file->store('', 'course_materials');
                    
                    $fileData['file_path'] = $path;
                    $fileData['file_name'] = $file->getClientOriginalName();
                    $fileData['file_size'] = $this->formatFileSize($file->getSize());
                }

                if (isset($material['id'])) {
                    // Update existing material
                    $existingMaterial = CourseMaterial::find($material['id']);
                    if ($existingMaterial) {
                        $existingMaterial->update($fileData);
                        $existingMaterialIds[] = $existingMaterial->id;
                    }
                } else {
                    // Create new material
                    $newMaterial = $course->materials()->create($fileData);
                    $existingMaterialIds[] = $newMaterial->id;
                }
            }

            // Delete materials not included in the update
            $course->materials()->whereNotIn('id', $existingMaterialIds)->delete();
        } else {
            // If no materials are sent, delete all existing ones
            $course->materials()->delete();
            }
        } else {
            // This is a main course - only validate title
            $validated = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $course->update([
                'title' => $validated['title']
            ]);
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Delete associated materials first
        $course->materials()->delete();
        $course->delete();

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