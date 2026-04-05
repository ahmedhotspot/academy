<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayroll extends Model
{
    use BranchScoped;

    protected $table = 'teacher_payrolls';

    protected $fillable = [
        'branch_id',
        'teacher_id',
        'month',
        'year',
        'base_salary',
        'deduction_amount',
        'penalty_amount',
        'bonus_amount',
        'final_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'base_salary'      => 'decimal:2',
            'deduction_amount' => 'decimal:2',
            'penalty_amount'   => 'decimal:2',
            'bonus_amount'     => 'decimal:2',
            'final_amount'     => 'decimal:2',
        ];
    }

    // =====================================================
    // ثوابت الحالات
    // =====================================================

    public const STATUSES = ['غير مصروف', 'مصروف'];

    // =====================================================
    // العلاقات
    // =====================================================

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // =====================================================
    // Accessors — Formatting و حسابات
    // =====================================================

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'مصروف' ? 'bg-success' : 'bg-warning text-dark';
    }

    public function getFormattedBaseSalaryAttribute(): string
    {
        return number_format($this->base_salary, 2) . ' ج';
    }

    public function getFormattedDeductionAttribute(): string
    {
        return number_format($this->deduction_amount, 2) . ' ج';
    }

    public function getFormattedPenaltyAttribute(): string
    {
        return number_format($this->penalty_amount, 2) . ' ج';
    }

    public function getFormattedBonusAttribute(): string
    {
        return number_format($this->bonus_amount, 2) . ' ج';
    }

    public function getFormattedFinalAttribute(): string
    {
        return number_format($this->final_amount, 2) . ' ج';
    }

    public function getMonthYearAttribute(): string
    {
        $monthNames = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                       'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        return $monthNames[$this->month] . ' ' . $this->year;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->deduction_amount + $this->penalty_amount;
    }

    public function getAdjustmentsAttribute(): float
    {
        return $this->bonus_amount - ($this->deduction_amount + $this->penalty_amount);
    }
}

