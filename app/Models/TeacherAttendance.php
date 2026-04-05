<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    use BranchScoped;

    protected $table = 'teacher_attendances';

    protected $fillable = [
        'branch_id',
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

    public const STATUSES = ['حاضر', 'غائب', 'متأخر', 'بعذر'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'حاضر' => 'bg-success',
            'غائب' => 'bg-danger',
            'متأخر' => 'bg-warning text-dark',
            'بعذر' => 'bg-info text-dark',
            default => 'bg-secondary',
        };
    }
}

