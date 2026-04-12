<?php

namespace App\Models;

use App\Traits\BranchScoped;
use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guardian extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, BranchScoped;

    protected $table = 'guardians';

    protected $fillable = [
        'branch_id',
        'full_name',
        'phone',
        'whatsapp',
        'status',
        'password',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    protected static function newFactory(): GuardianFactory
    {
        return GuardianFactory::new();
    }
}

