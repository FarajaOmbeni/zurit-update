<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyMarketFund extends Model
{
    protected $fillable = [
        'label',
        'value',
        'return',
    ];
}
