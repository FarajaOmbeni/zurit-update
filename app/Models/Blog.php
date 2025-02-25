<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'blog_tag', 'blog_image', 'blog_title', 'slug' ,'blog_message'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
