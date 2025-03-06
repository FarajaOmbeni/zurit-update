<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'start_date',
        'target_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    // A goal belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A goal has many contributions
    public function contributions()
    {
        return $this->hasMany(GoalContribution::class);
    }
}
