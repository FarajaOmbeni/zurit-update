<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'description',
        'value',
        'acquisition_date',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'value' => 'decimal:2',
    ];

    // An asset belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
