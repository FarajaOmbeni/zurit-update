<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPlanner extends Model
{
    use HasFactory;
    protected $table = 'investments';

    protected $fillable = [
        'user_id',
        'initial_investment',
        'total_investment',
        'investment_type',
        'monthly_contribution',
        'number_of_months',
        'number_of_years',
        'number_of_days',
        'rate_of_return',
        'details_of_investment',
        'mmf_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withholdingTax()
    {
        return $this->belongsTo(WithholdingTax::class);
    }


    public function asset()
    {
        return $this->hasOne(Asset::class, 'user_id', 'user_id');
    }




    protected static function booted()
    {
        static::deleting(function ($investment) {
            $investment->asset()->delete();
        });
    }
}
