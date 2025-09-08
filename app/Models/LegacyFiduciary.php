<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyFiduciary extends Model
{
    protected $fillable = [
        'user_id',
        'executors',
        'trustees',
        'guardians',
        'witness_placeholders',
    ];

    protected $casts = [
        'executors' => 'array',
        'trustees' => 'array',
        'guardians' => 'array',
        'witness_placeholders' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
