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

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function liabilities()
    {
        return $this->hasMany(Liability::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function calculateNetWorth()
    {
        $assetsTotal = $this->assets()->sum('value');
        $liabilitiesTotal = $this->liabilities()->sum('amount');
        $debtsTotal = $this->debts()->sum('current_amount');

        return $assetsTotal - ($liabilitiesTotal + $debtsTotal);
    }

}
