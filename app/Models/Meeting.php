<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'coach_id',
        'client_id',
        'zoom_id',
        'join_url',
        'start_url',
        'start_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    /**
     * The coach who scheduled the meeting.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * The client who will attend the meeting.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
