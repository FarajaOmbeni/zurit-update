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
        'notes',
    ];

    protected $casts = [
        'contribution_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
