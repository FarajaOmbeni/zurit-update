<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
