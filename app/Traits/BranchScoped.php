<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait BranchScoped
 *
 * يضيف فلترة تلقائية حسب الفرع (branch) للموديل.
 * تُستخدم لضمان أن كل موديل يعود فقط إلى بيانات فرعه.
 */
trait BranchScoped
{
    /**
     * Cache auth branch context to avoid repeated DB hits in the same request.
     *
     * @var array{authenticated: bool, is_super_admin: bool, branch_id: int|null}|null
     */
    private static ?array $cachedAuthBranchContext = null;

    /**
     * Boot the trait.
     */
    public static function bootBranchScoped(): void
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            $context = self::resolveAuthBranchContext();

            if (! $context['authenticated'] || $context['is_super_admin'] || ! $context['branch_id']) {
                return;
            }

            $builder->where(self::qualifiedBranchColumn($builder), $context['branch_id']);
        });
    }

    /**
     * فلترة حسب فرع محدد.
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where(self::qualifiedBranchColumn($query), $branchId);
    }

    /**
     * فلترة حسب الفرع الحالي للمستخدم المسجل.
     */
    public function scopeCurrentBranch(Builder $query): Builder
    {
        $context = self::resolveAuthBranchContext();

        if ($context['authenticated'] && $context['branch_id']) {
            return $query->where(self::qualifiedBranchColumn($query), $context['branch_id']);
        }

        return $query;
    }

    /**
     * إضافة البيانات بدون فلترة الفروع.
     */
    public function scopeWithoutBranchFilter(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }

    private static function qualifiedBranchColumn(Builder $query): string
    {
        return $query->getModel()->getTable() . '.branch_id';
    }

    /**
     * Resolve auth branch context without using Eloquent User model.
     * لتفادي أي استدعاء دائري عند تطبيق الـ scope على موديل User نفسه.
     *
     * @return array{authenticated: bool, is_super_admin: bool, branch_id: int|null}
     */
    private static function resolveAuthBranchContext(): array
    {
        if (self::$cachedAuthBranchContext !== null) {
            return self::$cachedAuthBranchContext;
        }

        $userId = auth()->id();

        if (! $userId) {
            return self::$cachedAuthBranchContext = [
                'authenticated' => false,
                'is_super_admin' => false,
                'branch_id' => null,
            ];
        }

        $branchIdValue = DB::table('users')->where('id', $userId)->value('branch_id');
        $branchId = is_null($branchIdValue) ? null : (int) $branchIdValue;

        $isSuperAdminByRole = DB::table('model_has_roles as mhr')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->where('mhr.model_id', $userId)
            ->where('mhr.model_type', User::class)
            ->where('r.name', 'المشرف العام')
            ->exists();

        return self::$cachedAuthBranchContext = [
            'authenticated' => true,
            'is_super_admin' => is_null($branchId) || $isSuperAdminByRole,
            'branch_id' => $branchId,
        ];
    }
}

