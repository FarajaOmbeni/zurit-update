<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'description',
        'amount',
        'due_date',
    ];

    // Debt belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A debt has many payments
    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}
