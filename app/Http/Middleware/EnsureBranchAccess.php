<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureBranchAccess
 *
 * التحقق من أن المستخدم يمكنه الوصول فقط لبيانات فرعه
 * - المشرفون العامون (branch_id = null) يروا جميع البيانات
 * - مديرو الفروع يروا بيانات فرعهم فقط
 */
class EnsureBranchAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // إذا كان مشرفًا عامًا (بدون فرع)، اسمح بالوصول الكامل
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // إذا كان لديه فرع، علّم الطلب بـ branch_id
            if ($user->branch_id) {
                $request->merge(['user_branch_id' => $user->branch_id]);
            }
        }

        return $next($request);
    }
}

