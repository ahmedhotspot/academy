<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use BranchScoped;

    protected $table = 'assessments';

    protected $fillable = [
        'branch_id',
        'student_id',
        'group_id',
        'teacher_id',
        'assessment_date',
        'type',
        'memorization_result',
        'tajweed_result',
        'tadabbur_result',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date'      => 'date',
            'memorization_result'  => 'decimal:2',
            'tajweed_result'       => 'decimal:2',
            'tadabbur_result'      => 'decimal:2',
        ];
    }

    // =====================================================
    // ثوابت أنواع الاختبارات
    // =====================================================

    public const TYPES = ['أسبوعي', 'شهري', 'ختم جزء'];
    public const MAX_SCORE = 100;

    // =====================================================
    // العلاقات
    // =====================================================

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // =====================================================
    // Accessors — نسب المئة والـ Badges
    // =====================================================

    public function getAverageScoreAttribute(): ?float
    {
        $scores = [];
        if ($this->memorization_result !== null) $scores[] = $this->memorization_result;
        if ($this->tajweed_result !== null) $scores[] = $this->tajweed_result;
        if ($this->tadabbur_result !== null) $scores[] = $this->tadabbur_result;

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : null;
    }

    public function getScoreBadgeClass(float|int|null $score): string
    {
        if ($score === null) return 'bg-secondary';

        return match (true) {
            $score >= 90 => 'bg-success',
            $score >= 80 => 'bg-primary',
            $score >= 70 => 'bg-info text-dark',
            $score >= 60 => 'bg-warning text-dark',
            default      => 'bg-danger',
        };
    }

    public function getMemorizationBadgeClassAttribute(): string
    {
        return $this->getScoreBadgeClass($this->memorization_result);
    }

    public function getTajweedBadgeClassAttribute(): string
    {
        return $this->getScoreBadgeClass($this->tajweed_result);
    }

    public function getTadaburBadgeClassAttribute(): string
    {
        return $this->getScoreBadgeClass($this->tadabbur_result);
    }

    public function getAverageBadgeClassAttribute(): string
    {
        return $this->getScoreBadgeClass($this->average_score);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'أسبوعي'  => 'اختبار أسبوعي',
            'شهري'    => 'اختبار شهري',
            'ختم جزء' => 'اختبار ختم جزء',
            default   => $this->type,
        };
    }
}

