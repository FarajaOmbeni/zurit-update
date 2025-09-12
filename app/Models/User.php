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
        'coach_id',
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
        'role' => 'integer',
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

    // A user has many quiz attempts
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function canViewMaterial(CourseMaterial $material): bool
    {
        // Basic implementation - adjust according to your business logic
        return true; // Replace with your actual authorization logic

        // Example implementations:
        // 1. If all authenticated users can view:
        // return true;

        // 2. If you have course enrollment:
        // return $this->enrolledCourses()->where('course_id', $material->course_id)->exists();

        // 3. If you have role-based access:
        // return $this->hasRole('student') || $this->hasRole('teacher');
    }

    /**
     * Get the coach assigned to this user
     */
    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function coachProfile()
    {
        return $this->hasOne(Coach::class, 'email', 'email');
    }

    // MSME Financial Management Relationships

    // A user has one business profile
    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    // A user has many cashflow entries
    public function cashflowEntries()
    {
        return $this->hasMany(CashflowEntry::class);
    }

    // A user has many profit & loss records
    public function profitLossRecords()
    {
        return $this->hasMany(ProfitLossRecord::class);
    }

    // A user has many balance sheet records
    public function balanceSheetRecords()
    {
        return $this->hasMany(BalanceSheetRecord::class);
    }

    // A user has many pricing models
    public function pricingModels()
    {
        return $this->hasMany(PricingModel::class);
    }

    // A user has many business plans
    public function businessPlans()
    {
        return $this->hasMany(BusinessPlan::class);
    }

    // A user has many financial projections
    public function financialProjections()
    {
        return $this->hasMany(FinancialProjection::class);
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
