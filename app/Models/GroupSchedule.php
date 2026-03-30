<?php

namespace App\Models;

use Database\Factories\GroupScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSchedule extends Model
{
    use HasFactory;

    protected $table = 'group_schedules';

    protected $fillable = [
        'group_id',
        'day_name',
        'start_time',
        'end_time',
        'status',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    protected static function newFactory(): GroupScheduleFactory
    {
        return GroupScheduleFactory::new();
    }
}

