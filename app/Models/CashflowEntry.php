<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashflowEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // 'income' or 'expense'
        'category',
        'subcategory',
        'amount',
        'payment_method', // 'cash', 'bank', 'mpesa', 'credit', etc.
        'description',
        'reference_number',
        'entry_date',
        'is_recurring',
        'business_unit', // for businesses with multiple units/departments
        'invoice_number',
        'customer_supplier',
        'vat_amount',
        'tax_amount',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    // Belongs to a user/business
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for income entries
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    // Scope for expense entries
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    // Scope for date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    // Calculate net amount (amount - taxes)
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->vat_amount - $this->tax_amount;
    }
} 