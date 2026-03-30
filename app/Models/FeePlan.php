<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePlan extends Model
{
    protected $table = 'fee_plans';

    protected $fillable = [
        'name',
        'payment_cycle',
        'amount',
        'has_sisters_discount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount'               => 'decimal:2',
            'has_sisters_discount' => 'boolean',
        ];
    }

    // =====================================================
    // ثوابت الدورات المالية والحالات
    // =====================================================

    public const PAYMENT_CYCLES = ['شهري', 'نصف شهري', 'أسبوعي', 'بالحلقة'];
    public const STATUSES = ['active', 'inactive'];

    // =====================================================
    // العلاقات
    // =====================================================

    public function studentSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentSubscription::class);
    }


    // =====================================================
    // Accessors — Labels و Badges
    // =====================================================

    public function getPaymentCycleLabelAttribute(): string
    {
        return match ($this->payment_cycle) {
            'شهري'     => 'الدفع الشهري',
            'نصف شهري' => 'الدفع نصف الشهري',
            'أسبوعي'   => 'الدفع الأسبوعي',
            'بالحلقة'  => 'الدفع بالحلقة',
            default    => $this->payment_cycle,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    public function getDiscountLabelAttribute(): string
    {
        return $this->has_sisters_discount ? 'نعم' : 'لا';
    }

    public function getDiscountBadgeClassAttribute(): string
    {
        return $this->has_sisters_discount ? 'bg-info' : 'bg-light text-dark';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ر.س';
    }
}

