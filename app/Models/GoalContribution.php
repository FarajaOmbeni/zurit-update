<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoalContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'transaction_id',
        'amount',
        'contribution_date',
        'notes',
    ];

    protected $casts = [
        'contribution_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
