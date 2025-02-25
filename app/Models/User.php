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
        'google_id',
        'phone',
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

/**
 * Determine if the user has the given permission.
 *
 * @param  string  $permission
 * @return bool
 */
public function hasPermission(string $permission): bool
{
    // Hard-coded permissions for each user role
    $permissions = [
        '2' => ['book-list', 'book-create', 'book-edit', 'book-delete'],
        '1' => ['book-list', 'book-edit'],
        '0' => ['book-list'],
    ];

    // Get the user's role
    $role = $this->role;

    // Check if the user's role has the given permission
    return in_array($permission, $permissions[$role] ?? []);
}

/**
 * fetch user's assets before deleting the user
 *
 * 
 */
    public function incomes(){
        return $this->hasMany(Income::class);
    }

    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function assets(){
        return $this->hasMany(Asset::class);
    }

    public function liabilities(){
        return $this->hasMany(Liability::class);
    }

    public function debts(){
        return $this->hasMany(Debt::class);
    }

    public function goals(){
        return $this->hasMany(Goal::class);
    }

    public function investments()
    {
        return $this->hasMany(InvestmentPlanner::class);
    }
    
    // public function marketingMessages()
    // {
    //     return $this->hasMany(MarketingMessage::class);
    // }

    public function extraPayments()
    {
        return $this->hasMany(ExtraPayment::class);
    }

}
