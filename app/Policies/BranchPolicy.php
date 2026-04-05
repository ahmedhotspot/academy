<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class BranchPolicy
 *
 * سياسة التحقق من صلاحيات الوصول لبيانات الفروع
 */
class BranchPolicy
{
    /**
     * التحقق من أن المستخدم يمكنه عرض بيانات الفرع
     */
    public function view(User $user, ?int $branchId = null): bool
    {
        // المشرفون العامون يروا كل الفروع
        if ($user->isSuperAdmin()) {
            return true;
        }

        // مديرو الفروع يروا فرعهم فقط
        return $user->branch_id === $branchId;
    }

    /**
     * التحقق من أن المستخدم يمكنه تعديل بيانات الفرع
     */
    public function update(User $user, ?int $branchId = null): bool
    {
        // المشرفون العامون يمكنهم تعديل أي فرع
        if ($user->isSuperAdmin()) {
            return true;
        }

        // مديرو الفروع يمكنهم تعديل فرعهم فقط
        return $user->branch_id === $branchId;
    }

    /**
     * التحقق من أن المستخدم يمكنه حذف بيانات الفرع
     */
    public function delete(User $user, ?int $branchId = null): bool
    {
        // المشرفون العامون فقط يمكنهم الحذف
        return $user->isSuperAdmin();
    }

    /**
     * التحقق من أن المستخدم يمكنه عرض التقارير
     */
    public function viewReports(User $user, ?int $branchId = null): bool
    {
        // المشرفون العامون يروا كل التقارير
        if ($user->isSuperAdmin()) {
            return true;
        }

        // مديرو الفروع يروا تقارير فرعهم فقط
        return $user->branch_id === $branchId;
    }
}

