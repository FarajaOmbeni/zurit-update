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
        'phone',
        'role',
        'password',
        'coach_id',
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
}
