<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * AdminController — الـ Base Controller لجميع Controllers الإدارة
 *
 * كل Controller داخل Admin يجب أن يرث من هذا الـ Controller.
 * يوفّر:
 * - عنوان الصفحة $title
 * - مسار الـ View الأساسي $viewPath
 * - helper لإرجاع الـ views بسهولة
 */
abstract class AdminController extends Controller
{
    /**
     * عنوان الصفحة — يظهر في الـ <title> وفي header الصفحة
     */
    protected string $title = '';

    /**
     * المسار الأساسي للـ views الخاصة بهذا الـ Controller
     * مثال: 'admin.students'
     */
    protected string $viewPath = '';

    /**
     * إرجاع View مع دمج بيانات الـ title والـ breadcrumbs تلقائيًا
     *
     * @param string $view  اسم الـ view (إذا كان فارغًا يستخدم $viewPath)
     * @param array  $data  البيانات المرسلة إلى الـ view
     */
    protected function adminView(string $view, array $data = []): View
    {
        return view($view, array_merge([
            'pageTitle' => $this->title,
            'userBranchId' => auth()->user()?->branch_id,
            'isSuperAdmin' => auth()->user()?->isSuperAdmin(),
        ], $data));
    }
}
