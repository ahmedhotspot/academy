<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSubscription extends Model
{
    protected $table = 'student_subscriptions';

    protected $fillable = [
        'student_id',
        'fee_plan_id',
        'amount',
        'discount_amount',
        'final_amount',
        'paid_amount',
        'remaining_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount'            => 'decimal:2',
            'discount_amount'   => 'decimal:2',
            'final_amount'      => 'decimal:2',
            'paid_amount'       => 'decimal:2',
            'remaining_amount'  => 'decimal:2',
        ];
    }

    // =====================================================
    // ثوابت الحالات
    // =====================================================

    public const STATUSES = ['نشط', 'متأخر', 'مكتمل', 'موقوف'];

    // =====================================================
    // العلاقات
    // =====================================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feePlan(): BelongsTo
    {
        return $this->belongsTo(FeePlan::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // =====================================================
    // Accessors — Labels و Badges
    // =====================================================

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'نشط'    => 'bg-success',
            'متأخر'  => 'bg-warning text-dark',
            'مكتمل'  => 'bg-info',
            'موقوف'  => 'bg-danger',
            default  => 'bg-secondary',
        };
    }

    public function getPaymentProgressAttribute(): int
    {
        if ($this->final_amount == 0) return 0;
        return (int) round(($this->paid_amount / $this->final_amount) * 100);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ر.س';
    }

    public function getFormattedFinalAmountAttribute(): string
    {
        return number_format($this->final_amount, 2) . ' ر.س';
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return number_format($this->paid_amount, 2) . ' ر.س';
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->remaining_amount, 2) . ' ر.س';
    }

    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_amount, 2) . ' ر.س';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'متأخر' && $this->remaining_amount > 0;
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->status === 'مكتمل' && $this->remaining_amount == 0;
    }
}

