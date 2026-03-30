<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    protected $table = 'teacher_attendances';

    protected $fillable = [
        'teacher_id',
        'attendance_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    // =====================================================
    // الحالات المعتمدة
    // =====================================================

    public const STATUSES = ['حاضر', 'غائب', 'متأخر', 'بعذر'];

    // =====================================================
    // العلاقات
    // =====================================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // =====================================================
    // Accessors
    // =====================================================

    /**
     * كلاس Badge لحالة الحضور (لاستخدامها في الـ Blade و DataTable)
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'حاضر'  => 'bg-success',
            'غائب'  => 'bg-danger',
            'متأخر' => 'bg-warning text-dark',
            'بعذر'  => 'bg-info text-dark',
            default => 'bg-secondary',
        };
    }
}

