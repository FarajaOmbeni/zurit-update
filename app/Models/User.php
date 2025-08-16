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
        'subscription_status',
        'subscription_expires_at',
        'last_payment_date',
        'subscription_package',
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
        'subscription_expires_at' => 'datetime',
        'last_payment_date' => 'datetime',
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

    // User subscription relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    // Subscription helper methods
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getSubscriptionStatus(): string
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return 'inactive';
        }

        if ($subscription->is_expired) {
            return 'expired';
        }

        return $subscription->status;
    }

    public function isSubscriptionExpiring(int $days = 3): bool
    {
        $subscription = $this->activeSubscription;
        return $subscription && $subscription->days_until_expiry <= $days;
    }
}
