<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'details_of_investment',
        'description',
        'initial_amount',
        'current_amount',
        'target_amount',
        'start_date',
        'target_date',
        'expected_return_rate',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
        'initial_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'expected_return_rate' => 'decimal:4',
    ];

    // An investment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An investment has many contributions
    public function contributions()
    {
        return $this->hasMany(InvestmentContribution::class);
    }

    /**  Automatically include the derived columns in JSON responses   */
    protected $appends = [
        'profit',            // Net income after tax
        'cumulative_amount', // Deposits + profit
    ];

    /** Net profit earned so far, _after tax is applied only to profit_ */
    public function getProfitAttribute(): float
    {
        // 1. Principal = deposits so far (initial + top-ups) – untouched by tax
        $principal = (float) $this->current_amount;

        if ($principal === 0.0) {
            return 0.0;            // avoid div-by-zero if something is off
        }

        // 2. Investment duration in fractional years
        $years = Carbon::parse($this->start_date)
            ->floatDiffInRealYears(Carbon::now());

        // 3. Gross profit before tax
        $rate        = ($this->expected_return_rate ?? 0) / 100;   // e.g. 0.136
        $grossProfit = $principal * $rate * $years;

        // 4. Work out the tax rate that applies **only to the profit**
        $taxRate = match ($this->type) {
            'bills', 'mmf'             => 0.15,                    // 15 % withholding
            'bonds' => $years < 5      ? 0.15 : 0.10,              // 15 % <5y, else 10 %
            default                    => 0.00,                    // assume no tax rule
        };

        // 5. Net profit = gross profit minus tax (principal untouched)
        $netProfit = $grossProfit * (1 - $taxRate);

        return round($netProfit, 2);   // match your decimal(15,2) storage style
    }


    /** Running balance (deposits + profit)  */
    public function getCumulativeAmountAttribute(): float
    {
        // principal (untaxed) + net profit (tax already removed)
        return round($this->current_amount + $this->profit, 2);
    }
}
