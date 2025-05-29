<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurrenceRule extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'type',
        'amount',
        'description',
        'pattern',
        'next_run_on',
        'is_active'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'rule_id');
    }
}


