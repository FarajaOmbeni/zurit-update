<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcourse_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'type'
    ];

    public function subcourse(): BelongsTo
    {
        return $this->belongsTo(Subcourse::class);
    }

    protected $hidden = [
        'file_path',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['is_video', 'display_url'];

    public function getIsVideoAttribute()
    {
        return $this->type === 'video';
    }

    public function getDisplayUrlAttribute()
    {
        if ($this->type === 'video' && $this->file_path) {
            return route('course-materials.video', $this->id);
        }
        return route('course-materials.show', $this->id);
    }
}