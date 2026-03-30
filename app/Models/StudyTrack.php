<?php

namespace App\Models;

use Database\Factories\StudyTrackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyTrack extends Model
{
    use HasFactory;

    protected $table = 'study_tracks';

    protected $fillable = [
        'name',
        'status',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    protected static function newFactory(): StudyTrackFactory
    {
        return StudyTrackFactory::new();
    }
}

