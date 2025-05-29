<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // A user has many incomes
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    // A user has many expenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // A user has many goals
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    // A user has many investments
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    // A user has many debts
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    // A user has many assets
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    // A user has many liabilities
    public function liabilities()
    {
        return $this->hasMany(Liability::class);
    }

    // A user has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mpesaPayments()
    {
        return $this->hasMany(MpesaPayment::class);
    }

    public function latestValidSubscription(): ?MpesaPayment
    {
        return $this->mpesaPayments()
            ->where('purpose', 'subscription')
            ->where('result_code', 0)
            ->whereNotNull('mpesa_receipt_number')
            ->orderByDesc('created_at')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        $payment = $this->latestValidSubscription();

        return $payment &&
            $payment->created_at->greaterThan(now()->subDays(31));
    }

}
