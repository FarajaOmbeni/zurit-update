<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'amount',
        'description',
        'expense_date',
    ];

    // Expense belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Expense is a transaction
    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
