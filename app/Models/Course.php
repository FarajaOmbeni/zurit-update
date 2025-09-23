<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
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

    public function subCourses(): HasMany
    {
        return $this->hasMany(Subcourse::class);
    }
}
