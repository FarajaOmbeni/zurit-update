<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithholdingTax extends Model
{
    use HasFactory;
    protected $table = 'withholding_tax_rates';

    protected $fillable = [
        'investment_type',
        'tax_rate',
    ];
}
