<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyFiduciary extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'institution_type',
        'institution_name',
        'contact_name',
        'email',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
