<?php

namespace App\Models;

use App\Enums\BranchStatusEnum;
use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => BranchStatusEnum::class,
    ];

    /**
     * تحديد الـ Factory الخاص بهذا النموذج صراحةً
     */
    protected static function newFactory(): BranchFactory
    {
        return BranchFactory::new();
    }

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * المستخدمون المنتمون لهذا الفرع
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * الطلاب المرتبطون بهذا الفرع
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * أولياء الأمور المرتبطون بهذا الفرع
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    /**
     * الحلقات المرتبطة بهذا الفرع
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    // =====================================================
    // Scopes
    // =====================================================

    /**
     * فلترة الفروع النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('status', BranchStatusEnum::Active);
    }

    // =====================================================
    // Accessors / Helpers
    // =====================================================

    /**
     * هل الفرع نشط؟
     */
    public function isActive(): bool
    {
        return $this->status === BranchStatusEnum::Active;
    }

    /**
     * اسم الحالة باللغة العربية
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? 'غير معروف';
    }

    /**
     * كلاس الـ badge المناسب للحالة
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status?->badgeClass() ?? 'bg-secondary';
    }
}

