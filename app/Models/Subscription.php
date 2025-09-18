<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package',
        'status',
        'amount',
        'currency',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'order_id',
        'order_tracking_id',
        'order_merchant_reference',
        'pesapal_transaction_id',
        'payment_status',
        'payment_method',
        'payment_completed_at',
        'auto_renew',
        'renewal_attempts',
        'next_renewal_date',
        'last_renewal_attempt_at',
        'renewal_errors',
        'parent_subscription_id',
        'is_renewal',
        'pesapal_response',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'next_renewal_date' => 'datetime',
        'last_renewal_attempt_at' => 'datetime',
        'auto_renew' => 'boolean',
        'is_renewal' => 'boolean',
        'renewal_errors' => 'array',
        'pesapal_response' => 'array',
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentSubscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'parent_subscription_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(Subscription::class, 'parent_subscription_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpiring($query, $days = 3)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('auto_renew', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<', now());
    }

    public function scopeForRenewal($query)
    {
        return $query->where('auto_renew', true)
            ->where('status', 'active')
            ->where('next_renewal_date', '<=', now());
    }

    public function scopeFailedRenewals($query, $maxAttempts = 3)
    {
        return $query->where('renewal_attempts', '>=', $maxAttempts)
            ->where('status', 'active');
    }

    // Accessors & Mutators
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' &&
            $this->expires_at &&
            $this->expires_at->isFuture();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expires_at) return 0;
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getIsExpiringAttribute(): bool
    {
        return $this->is_active && $this->days_until_expiry <= 3;
    }

    // Methods
    public function activate(array $paymentData = []): bool
    {
        $packageDetails = $this->getPackageDetails();

        $this->update([
            'status' => 'active',
            'payment_status' => 'completed',
            'payment_completed_at' => now(),
            'starts_at' => now(),
            'expires_at' => now()->addDays($packageDetails['duration_days']),
            'next_renewal_date' => now()->addDays($packageDetails['duration_days'] - 3), // Renew 3 days before expiry
            'pesapal_response' => $paymentData,
        ]);

        // Update user subscription status
        $this->user->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => $this->expires_at,
            'subscription_package' => $this->package,
            'last_payment_date' => now(),
        ]);

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
            'notes' => $reason,
        ]);

        // Update user status if this is their active subscription
        if ($this->user->subscription_status === 'active') {
            $this->user->update(['subscription_status' => 'cancelled']);
        }

        return true;
    }

    public function expire(): bool
    {
        $this->update([
            'status' => 'expired',
            'auto_renew' => false,
        ]);

        // Update user status if this is their active subscription
        if ($this->user->subscription_status === 'active') {
            $this->user->update(['subscription_status' => 'expired']);
        }

        return true;
    }

    public function incrementRenewalAttempt(array $errorData = []): void
    {
        $renewalErrors = $this->renewal_errors ?? [];
        $renewalErrors[] = [
            'attempt' => $this->renewal_attempts + 1,
            'error' => $errorData,
            'attempted_at' => now()->toISOString(),
        ];

        $this->update([
            'renewal_attempts' => $this->renewal_attempts + 1,
            'last_renewal_attempt_at' => now(),
            'renewal_errors' => $renewalErrors,
        ]);
    }

    public function createRenewalSubscription(): Subscription
    {
        return self::create([
            'user_id' => $this->user_id,
            'package' => $this->package,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'auto_renew' => true,
            'parent_subscription_id' => $this->id,
            'is_renewal' => true,
            'order_id' => 'RENEWAL_' . strtoupper(uniqid()),
        ]);
    }

    public function getPackageDetails(): array
    {
        $packages = [
            'monthly' => [
                'name' => 'Monthly Subscription',
                'duration_days' => 31,
                'price' => 500
            ],
            'yearly' => [
                'name' => 'Yearly Subscription',
                'duration_days' => 365,
                'price' => 4500
            ]
        ];

        return $packages[$this->package] ?? $packages['monthly'];
    }

    // Static methods
    public static function createForUser(User $user, string $package, string $orderId): self
    {
        $packageDetails = (new self())->getPackageDetails();

        return self::create([
            'user_id' => $user->id,
            'package' => $package,
            'amount' => $packageDetails['price'],
            'currency' => 'KES',
            'order_id' => $orderId,
            'auto_renew' => true,
        ]);
    }

    public static function findByOrderId(string $orderId): ?self
    {
        return self::where('order_id', $orderId)->first();
    }

    public static function findByTrackingId(string $trackingId): ?self
    {
        return self::where('order_tracking_id', $trackingId)->first();
    }
}
