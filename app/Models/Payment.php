<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'student_id',
        'student_subscription_id',
        'payment_date',
        'amount',
        'receipt_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    // =====================================================
    // العلاقات
    // =====================================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(StudentSubscription::class, 'student_subscription_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            if (! auth()->check() || auth()->user()?->isSuperAdmin()) {
                return;
            }

            $branchId = auth()->user()?->branch_id;

            if ($branchId) {
                $builder->whereHas('student', fn (Builder $query) => $query->where('branch_id', $branchId));
            }
        });
    }

    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->whereHas('student', fn (Builder $studentQuery) => $studentQuery->where('branch_id', $branchId));
    }

    public function scopeCurrentBranch(Builder $query): Builder
    {
        $branchId = auth()->user()?->branch_id;

        if (! $branchId) {
            return $query;
        }

        return $query->forBranch($branchId);
    }

    public function scopeWithoutBranchFilter(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }

    // =====================================================
    // Accessors — Formatting
    // =====================================================

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ج';
    }

    public function getReceiptFormattedAttribute(): string
    {
        return 'إيصال #' . $this->receipt_number;
    }

    public function getFormattedPaymentDateAttribute(): string
    {
        return optional($this->payment_date)->format('Y-m-d');
    }
}

