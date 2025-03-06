<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'amount',
        'description',
        'income_date',
    ];

    // Income belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Income is a transaction
    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}

