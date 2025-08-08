<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class SetCourseOrderSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all main courses
        $mainCourses = Course::whereNull('parent_id')->get();

        foreach ($mainCourses as $mainCourse) {
            // Get all sub-courses for this main course and set their order
            $subCourses = Course::where('parent_id', $mainCourse->id)->orderBy('id')->get();
            
            foreach ($subCourses as $index => $subCourse) {
                $subCourse->update(['order' => $index + 1]);
            }
        }

        $this->command->info('Course order has been set for all sub-courses.');
    }
}
