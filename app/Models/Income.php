<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'source',
        'amount',
        'frequency',
        'income_date',
        'is_recurring',
    ];

    protected $casts = [
        'income_date' => 'date',
        'is_recurring' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}

