<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'parent_id',
        'order'
    ];

    protected static function booted()
    {
        static::creating(function ($course) {
            $course->slug = \Str::slug($course->title);
        });

        static::updating(function ($course) {
            $course->slug = \Str::slug($course->title);
        });
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'parent_id');
    }

    public function subCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'parent_id');
    }

    public function isMainCourse(): bool
    {
        return is_null($this->parent_id);
    }

    public function isSubCourse(): bool
    {
        return !is_null($this->parent_id);
    }
}