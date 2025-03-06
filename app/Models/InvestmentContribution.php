<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvestmentContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'transaction_id',
        'amount',
        'contribution_date',
    ];

    protected $casts = [
        'contribution_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // An investment contribution belongs to an investment
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    // An investment contribution belongs to a transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
