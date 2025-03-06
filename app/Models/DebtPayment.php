<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DebtPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'debt_id',
        'transaction_id',
        'amount',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // A debt payment belongs to a debt
    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    // A debt payment belongs to a transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
