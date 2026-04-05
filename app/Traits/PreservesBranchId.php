<?php

namespace App\Traits;

/**
 * Trait PreservesBranchId
 *
 * Trait للتأكد من أن branch_id يتم الحفاظ عليه عند الحفظ والتحديث
 * يضيف branch_id من المستخدم الحالي تلقائيًا إلى البيانات
 */
trait PreservesBranchId
{
    /**
     * التأكد من حفظ branch_id من المستخدم الحالي
     *
     * @param array $data
     * @return array
     */
    protected function ensureBranchId(array $data): array
    {
        // إذا كان المستخدم لديه فرع، أضفه للبيانات
        if (auth()->check() && auth()->user()->branch_id && !isset($data['branch_id'])) {
            $data['branch_id'] = auth()->user()->branch_id;
        }

        return $data;
    }

    /**
     * التأكد من أن البيانات تنتمي لفرع المستخدم الحالي
     *
     * @param array $data
     * @return bool
     */
    protected function validateBranchOwnership(array $data): bool
    {
        // المشرفون العامون يمكنهم العمل مع أي فرع
        if (auth()->user()?->isSuperAdmin()) {
            return true;
        }

        // تأكد من أن البيانات تنتمي لفرع المستخدم
        if (isset($data['branch_id']) && auth()->user()->branch_id) {
            return $data['branch_id'] == auth()->user()->branch_id;
        }

        // إذا لم يكن هناك تحديد للفرع، تأكد من المستخدم
        return auth()->user()->branch_id !== null;
    }
}

