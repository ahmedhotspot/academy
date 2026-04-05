<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Database\Factories\StudentEnrollmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEnrollment extends Model
{
    use HasFactory, BranchScoped;

    protected $table = 'student_enrollments';

    protected $fillable = [
        'branch_id',
        'student_id',
        'group_id',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'نشط',
            'transferred' => 'منقول',
            'suspended' => 'موقوف',
            default => 'غير محدد',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'transferred' => 'bg-info',
            'suspended' => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    protected static function newFactory(): StudentEnrollmentFactory
    {
        return StudentEnrollmentFactory::new();
    }
}
