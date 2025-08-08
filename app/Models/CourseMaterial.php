<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'file_path',
        'file_name',
        'file_size'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

protected $hidden = [
    'file_path',
    'created_at',
    'updated_at'
];
}