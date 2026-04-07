<?php

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\StudentSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * خدمة إنشاء الإشعارات تلقائياً عند تحميل الصفحة
 * بدون Cron Jobs — تُنفَّذ عبر View Composer
 */
class NotificationAutoCheckService
{
    /**
     * فحص الاشتراكات التي تقترب مواعيدها وإنشاء الإشعارات المطلوبة
     * بناءً على تاريخ الاستحقاق بدون الاشتراط على المبلغ المتبقي
     */
    public function checkAndCreateDueReminders(): void
    {
        // نستخدم Cache لتجنب التكرار الكثيف (مفتاح لكل ساعة)
        $cacheKey = 'subscription_reminders_checked_' . now()->format('Y-m-d-H');

        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            $today   = Carbon::today();
            $twoDays = $today->copy()->addDays(2);

            // الاشتراكات التي تاريخ استحقاقها متأخر أو خلال يومين (بدون اشتراط المبلغ المتبقي)
            // نستخدم withoutGlobalScopes لأننا نريد كل الفروع هنا
            $subscriptions = StudentSubscription::query()
                ->withoutGlobalScopes()
                ->with(['student'])
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<=', $twoDays)
                ->where('status', '!=', 'موقوف')
                ->get();

            foreach ($subscriptions as $subscription) {
                $branchId    = $subscription->branch_id;
                $studentName = $subscription->student?->full_name ?? 'طالب';
                $remaining   = number_format((float) $subscription->remaining_amount, 2) . ' ج';
                $dueDate     = $subscription->due_date?->format('Y-m-d') ?? '-';
                $isOverdue   = (bool) ($subscription->due_date && $subscription->due_date->startOfDay()->lt($today));

                // المستخدمون الذين يجب إرسال الإشعار إليهم
                $users = User::query()
                    ->withoutGlobalScopes()
                    ->whereNull('deleted_at')
                    ->when($branchId, function ($q) use ($branchId) {
                        $q->where(function ($inner) use ($branchId) {
                            $inner->where('branch_id', $branchId)
                                  ->orWhereNull('branch_id');
                        });
                    }, function ($q) {
                        // بدون branch_id في الاشتراك → أرسل للمشرف العام فقط
                        $q->whereNull('branch_id');
                    })
                    ->get();

                foreach ($users as $user) {
                    // تجنب التكرار
                    $alreadySent = Notification::query()
                        ->where('user_id', $user->id)
                        ->where('type', 'financial')
                        ->whereJsonContains('data->subscription_id', $subscription->id)
                        ->whereDate('created_at', $today)
                        ->exists();

                    if ($alreadySent) {
                        continue;
                    }

                    Notification::query()->create([
                        'user_id'   => $user->id,
                        'branch_id' => $branchId,
                        'type'      => 'financial',
                        'title'     => $isOverdue ? "متأخرات مالية: {$studentName}" : "تنبيه سداد: {$studentName}",
                        'message'   => $isOverdue
                            ? "الطالب {$studentName} - تاريخ الاستحقاق {$dueDate} (متأخر)"
                            : "الطالب {$studentName} - تاريخ الاستحقاق {$dueDate} (قريب)",
                        'data'      => [
                            'subscription_id' => $subscription->id,
                            'student_id'      => $subscription->student_id,
                            'student_name'    => $studentName,
                            'remaining'       => $remaining,
                            'due_date'        => $dueDate,
                            'is_overdue'      => $isOverdue,
                        ],
                        'is_read' => false,
                    ]);
                }
            }

            // احفظ في Cache حتى نهاية الساعة لتقليل الحمل وتحديث التنبيهات بشكل أسرع
            Cache::put($cacheKey, true, now()->endOfHour());

        } catch (\Throwable) {
            // لا تكسر الصفحة إذا حدث خطأ في الفحص
        }
    }

    /**
     * إنشاء إشعار فوري لاشتراك واحد بعد التعديل (بدون انتظار cache الساعة)
     * بناءً على تاريخ الاستحقاق بدون الاشتراط على المبلغ المتبقي
     */
    public function checkAndCreateDueReminderForSubscription(StudentSubscription $subscription): void
    {
        try {
            $today = Carbon::today();

            if (! $subscription->due_date || $subscription->status === 'موقوف') {
                return;
            }

            // بناءً على التاريخ فقط (بدون الاشتراط على المبلغ)
            if ($subscription->due_date->startOfDay()->gt($today->copy()->addDays(2))) {
                return;
            }

            $subscription->loadMissing('student');

            $branchId    = $subscription->branch_id;
            $studentName = $subscription->student?->full_name ?? 'طالب';
            $remaining   = number_format((float) $subscription->remaining_amount, 2) . ' ج';
            $dueDate     = $subscription->due_date?->format('Y-m-d') ?? '-';
            $isOverdue   = (bool) ($subscription->due_date && $subscription->due_date->startOfDay()->lt($today));

            $users = User::query()
                ->withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->when($branchId, function ($q) use ($branchId) {
                    $q->where(function ($inner) use ($branchId) {
                        $inner->where('branch_id', $branchId)
                            ->orWhereNull('branch_id');
                    });
                }, function ($q) {
                    $q->whereNull('branch_id');
                })
                ->get();

            foreach ($users as $user) {
                $alreadySent = Notification::query()
                    ->where('user_id', $user->id)
                    ->where('type', 'financial')
                    ->whereJsonContains('data->subscription_id', $subscription->id)
                    ->whereDate('created_at', $today)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                Notification::query()->create([
                    'user_id'   => $user->id,
                    'branch_id' => $branchId,
                    'type'      => 'financial',
                    'title'     => $isOverdue ? "متأخرات مالية: {$studentName}" : "تنبيه سداد: {$studentName}",
                    'message'   => $isOverdue
                        ? "الطالب {$studentName} - تاريخ الاستحقاق {$dueDate} (متأخر)"
                        : "الطالب {$studentName} - تاريخ الاستحقاق {$dueDate} (قريب)",
                    'data'      => [
                        'subscription_id' => $subscription->id,
                        'student_id'      => $subscription->student_id,
                        'student_name'    => $studentName,
                        'remaining'       => $remaining,
                        'due_date'        => $dueDate,
                        'is_overdue'      => $isOverdue,
                    ],
                    'is_read' => false,
                ]);
            }
        } catch (\Throwable) {
            // لا تكسر الطلب إذا فشل إنشاء الإشعار
        }
    }

    /**
     * عدد الإشعارات غير المقروءة للمستخدم الحالي
     */
    public function getUnreadCount(User $user): int
    {
        try {
            return $user->notifications()->where('is_read', false)->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * آخر الإشعارات للمستخدم (للقائمة المنسدلة)
     */
    public function getRecentNotifications(User $user, int $limit = 5): \Illuminate\Support\Collection
    {
        try {
            return $user->notifications()
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }
}

