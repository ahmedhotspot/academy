<?php

namespace App\Actions;

/**
 * BaseAction — الـ Base لجميع Actions في المشروع
 *
 * كل Action يجب أن يرث من هذا الـ Class أو ينفذ ActionInterface.
 * الاستخدام:
 *   class CreateStudentAction extends BaseAction { ... }
 *   $action->handle($data);
 */
abstract class BaseAction
{
    /**
     * تنفيذ الـ Action
     *
     * @param  array $data  البيانات المُمررة من الـ Controller أو الـ Request
     * @return mixed        النتيجة (Model أو bool أو أي قيمة مناسبة)
     */
    abstract public function handle(array $data): mixed;
}

