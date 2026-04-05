<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait BranchScoped
 *
 * يضيف فلترة تلقائية حسب الفرع (branch) للموديل.
 * تُستخدم لضمان أن كل موديل يعود فقط إلى بيانات فرعه.
 */
trait BranchScoped
{
    /**
     * Boot the trait
     * تطبيق الفلترة التلقائية عند تحميل الموديل
     */
    public static function bootBranchScoped()
    {
        static::addGlobalScope('branch', function (Builder $builder) {
            // إذا كان المستخدم الحالي مشرفًا عامًا (branch_id = null)، لا تطبق الفلترة
            if (auth()->check() && auth()->user()->isSuperAdmin()) {
                return;
            }

            // فلترة حسب الفرع الحالي للمستخدم
            if (auth()->check() && auth()->user()->branch_id) {
                $builder->where($builder->getModel()->getTable() . '.branch_id', auth()->user()->branch_id);
            }
        });
    }

    /**
     * فلترة حسب فرع محدد
     *
     * @param  Builder  $query
     * @param  int      $branchId
     * @return Builder
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where($this->getTable() . '.branch_id', $branchId);
    }

    /**
     * فلترة حسب الفرع الحالي للمستخدم المسجل
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeCurrentBranch(Builder $query): Builder
    {
        if (auth()->check() && auth()->user()->branch_id) {
            return $query->where($this->getTable() . '.branch_id', auth()->user()->branch_id);
        }

        return $query;
    }

    /**
     * إضافة البيانات بدون فلترة الفروع (للمشرفين العامين فقط)
     *
     * @return Builder
     */
    public function scopeWithoutBranchFilter(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }
}

