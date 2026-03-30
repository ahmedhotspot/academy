<?php

namespace App\Models;

use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'groups';

    protected $fillable = [
        'branch_id',
        'teacher_id',
        'study_level_id',
        'study_track_id',
        'name',
        'type',
        'schedule_type',
        'status',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studyLevel(): BelongsTo
    {
        return $this->belongsTo(StudyLevel::class);
    }

    public function studyTrack(): BelongsTo
    {
        return $this->belongsTo(StudyTrack::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(GroupSchedule::class);
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'individual' ? 'فردي' : 'مجموعة';
    }

    public function getScheduleTypeLabelAttribute(): string
    {
        return $this->schedule_type === 'daily' ? 'يومي' : 'أسبوعي';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    protected static function newFactory(): GroupFactory
    {
        return GroupFactory::new();
    }
}

