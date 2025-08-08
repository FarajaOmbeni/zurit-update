<?php

namespace App\Http\Controllers;

use App\Models\CourseMaterial;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseMaterialController extends Controller
{
    public function show(CourseMaterial $material)
{
    try {
        // 1. Verify file exists
        if (!Storage::disk('course_materials')->exists($material->file_path)) {
            \Log::error('PDF not found', [
                'material_id' => $material->id,
                'file_path' => $material->file_path,
                'storage_path' => Storage::disk('course_materials')->path($material->file_path)
            ]);
            abort(404, 'PDF file not found');
        }

        // 2. Get the actual file path
        $fullPath = Storage::disk('course_materials')->path($material->file_path);
        
        // 3. Verify file is readable
        if (!is_readable($fullPath)) {
            \Log::error('PDF not readable', [
                'path' => $fullPath,
                'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4)
            ]);
            abort(500, 'PDF file could not be accessed');
        }

        // 4. Return the file
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$material->file_name.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN'
        ]);

    } catch (\Exception $e) {
        \Log::error('PDF view error', [
            'error' => $e->getMessage(),
            'material' => $material->toArray()
        ]);
        abort(500, 'Error displaying PDF');
    }
}

public function viewer(CourseMaterial $material)
{
    // Simple check - anyone authenticated can view
    if (!auth()->check()) {
        abort(403);
    }

    return view('pdf.viewer', [
        'material' => $material
    ]);
}


}