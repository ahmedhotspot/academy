<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'username',
        'password',
        'avatar',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'status'            => UserStatusEnum::class,
        ];
    }

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * الفرع الذي ينتمي إليه المستخدم
     * null = مشرف عام يرى كل الفروع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * الحلقات التي يشرف عليها المعلم
     */
    public function teachingGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'teacher_id');
    }

    /**
     * سجلات حضور المعلم
     */
    public function teacherAttendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class, 'teacher_id');
    }

    /**
     * مستحقات المعلم
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(TeacherPayroll::class, 'teacher_id');
    }

    /**
     * إشعارات المستخدم
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // =====================================================
    // Scopes
    // =====================================================

    /**
     * المستخدمون النشطون فقط
     */
    public function scopeActive($query)
    {
        return $query->where('status', UserStatusEnum::Active);
    }

    /**
     * فلترة حسب الفرع
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // =====================================================
    // Helpers
    // =====================================================

    /**
     * هل المستخدم مشرف عام؟ (لا ينتمي لفرع بعينه)
     */
    public function isSuperAdmin(): bool
    {
        return is_null($this->branch_id);
    }

    /**
     * هل يمكن للمستخدم تسجيل الدخول؟
     */
    public function canLogin(): bool
    {
        return $this->status?->canLogin() ?? false;
    }

    /**
     * تسجيل وقت آخر دخول
     */
    public function recordLogin(): void
    {
        $this->updateQuietly(['last_login_at' => now()]);
    }

    /**
     * مسار الصورة الشخصية أو الصورة الافتراضية
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('dash/assets/images/user/avatar-default.png');
    }
}


