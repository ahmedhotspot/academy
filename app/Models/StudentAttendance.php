<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    use BranchScoped;

    protected $table = 'student_attendances';

    protected $fillable = [
        'branch_id',
        'student_id',
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

    public const STATUSES = ['حاضر', 'غائب', 'منقول'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'حاضر' => 'bg-success',
            'غائب' => 'bg-danger',
            'منقول' => 'bg-info',
            default => 'bg-secondary',
        };
    }
}

