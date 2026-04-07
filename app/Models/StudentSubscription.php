<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSubscription extends Model
{
    use BranchScoped;

    protected $table = 'student_subscriptions';

    protected $fillable = [
        'branch_id',
        'student_id',
        'fee_plan_id',
        'amount',
        'discount_amount',
        'final_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'start_date',
        'due_date',
        'remaining_due_date',
    ];

    protected function casts(): array
    {
        return [
            'amount'             => 'decimal:2',
            'discount_amount'    => 'decimal:2',
            'final_amount'       => 'decimal:2',
            'paid_amount'        => 'decimal:2',
            'remaining_amount'   => 'decimal:2',
            'start_date'         => 'date',
            'due_date'           => 'date',
            'remaining_due_date' => 'date',
        ];
    }

    // =====================================================
    // ثوابت الحالات
    // =====================================================

    public const STATUSES = ['نشط', 'متأخر', 'مكتمل', 'موقوف'];

    /**
     * Resolve financial status from remaining amount and due date.
     */
    public static function resolveFinancialStatus(float $remainingAmount, Carbon|string|null $dueDate = null): string
    {
        if ($remainingAmount <= 0) {
            return 'مكتمل';
        }

        if ($dueDate) {
            $due = $dueDate instanceof Carbon
                ? $dueDate->copy()->startOfDay()
                : Carbon::parse($dueDate)->startOfDay();

            if ($due->isPast()) {
                return 'متأخر';
            }
        }

        return 'نشط';
    }

    /**
     * Subscriptions that are financially overdue.
     */
    public function scopeFinanciallyOverdue(Builder $query): Builder
    {
        $today = now()->startOfDay()->toDateString();

        return $query
            ->where('remaining_amount', '>', 0)
            ->where(function (Builder $q) use ($today) {
                $q->where('status', 'متأخر')
                    ->orWhere(function (Builder $dateQuery) use ($today) {
                        $dateQuery->whereNotNull('due_date')
                            ->whereDate('due_date', '<', $today);
                    });
            });
    }

    // =====================================================
    // حساب تاريخ الاستحقاق بناءً على دورة الدفع
    // =====================================================

    public static function calculateDueDate(string $paymentCycle, Carbon $startDate): ?Carbon
    {
        return match ($paymentCycle) {
            'شهري'     => $startDate->copy()->addMonth(),
            'نصف شهري' => $startDate->copy()->addDays(15),
            'أسبوعي'   => $startDate->copy()->addWeek(),
            default    => null, // بالحلقة → يُدخل يدوياً
        };
    }

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
        return number_format($this->amount, 2) . ' ج';
    }

    public function getFormattedFinalAmountAttribute(): string
    {
        return number_format($this->final_amount, 2) . ' ج';
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return number_format($this->paid_amount, 2) . ' ج';
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->remaining_amount, 2) . ' ج';
    }

    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_amount, 2) . ' ج';
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->remaining_amount <= 0) {
            return false;
        }

        if ($this->status === 'متأخر') {
            return true;
        }

        return (bool) ($this->due_date?->isPast());
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->status === 'مكتمل' && $this->remaining_amount == 0;
    }

    /**
     * هل انتهت مدة الاشتراك؟ (تاريخ الاستحقاق في الماضي)
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * هل موعد سداد الباقي خلال يومين؟
     */
    public function getIsReminderDueAttribute(): bool
    {
        return $this->remaining_due_date
            && $this->remaining_amount > 0
            && $this->remaining_due_date->diffInDays(now(), false) >= -2
            && $this->remaining_due_date->isFuture();
    }

    /**
     * عدد الأيام المتبقية حتى تاريخ الاستحقاق
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (! $this->due_date) return null;
        return (int) now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
    }

    /**
     * عدد الأيام المتبقية حتى تاريخ استحقاق الباقي
     */
    public function getDaysUntilRemainingDueAttribute(): ?int
    {
        if (! $this->remaining_due_date) return null;
        return (int) now()->startOfDay()->diffInDays($this->remaining_due_date->startOfDay(), false);
    }
}
