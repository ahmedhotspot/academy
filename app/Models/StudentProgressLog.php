<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgressLog extends Model
{
    protected $table = 'student_progress_logs';

    protected $fillable = [
        'student_id',
        'group_id',
        'teacher_id',
        'progress_date',
        'memorization_amount',
        'revision_amount',
        'tajweed_evaluation',
        'tadabbur_evaluation',
        'repeated_mistakes',
        'mastery_level',
        'commitment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'progress_date' => 'date',
        ];
    }

    // =====================================================
    // ثوابت الحالات والقيم المعتمدة
    // =====================================================

    public const EVALUATION_LEVELS = ['ممتاز', 'جيد جداً', 'جيد', 'مقبول', 'ضعيف'];
    public const COMMITMENT_STATUSES = ['ملتزم', 'متأخر'];

    // =====================================================
    // العلاقات
    // =====================================================

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
    // Accessors — Badges
    // =====================================================

    public function getEvaluationBadgeClass(string $level): string
    {
        return match ($level) {
            'ممتاز'    => 'bg-success',
            'جيد جداً' => 'bg-primary',
            'جيد'      => 'bg-info text-dark',
            'مقبول'    => 'bg-warning text-dark',
            'ضعيف'     => 'bg-danger',
            default    => 'bg-secondary',
        };
    }

    public function getTajweedBadgeClassAttribute(): string
    {
        return $this->getEvaluationBadgeClass($this->tajweed_evaluation ?? '');
    }

    public function getTadaburBadgeClassAttribute(): string
    {
        return $this->getEvaluationBadgeClass($this->tadabbur_evaluation ?? '');
    }

    public function getMasteryBadgeClassAttribute(): string
    {
        return $this->getEvaluationBadgeClass($this->mastery_level ?? '');
    }

    public function getCommitmentBadgeClassAttribute(): string
    {
        return match ($this->commitment_status) {
            'ملتزم' => 'bg-success',
            'متأخر' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }
}

