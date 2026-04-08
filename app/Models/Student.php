<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, BranchScoped;

    protected $table = 'students';

    protected $fillable = [
        'branch_id',
        'guardian_id',
        'full_name',
        'enrollment_date',
        'birth_date',
        'age',
        'nationality',
        'identity_number',
        'identity_expiry_date',
        'gender',
        'residency_number',
        'residency_expiry_date',
        'phone',
        'whatsapp',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'birth_date' => 'date',
            'age' => 'integer',
            'identity_expiry_date' => 'date',
            'residency_expiry_date' => 'date',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function progressLogs(): HasMany
    {
        return $this->hasMany(StudentProgressLog::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(StudentSubscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function currentEnrollment(): ?StudentEnrollment
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->latest('created_at')
            ->first();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }
}

