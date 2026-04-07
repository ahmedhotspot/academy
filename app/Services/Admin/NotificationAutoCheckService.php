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
     */
    public function checkAndCreateDueReminders(): void
    {
        // نستخدم Cache لتجنب تكرار الفحص في نفس اليوم (مفتاح لكل يوم)
        $cacheKey = 'subscription_reminders_checked_' . now()->format('Y-m-d');

        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            $today   = Carbon::today();
            $twoDays = $today->copy()->addDays(2);

            // الاشتراكات التي تاريخ سداد الباقي خلال يومين + لا تزال لها باقي
            // نستخدم withoutGlobalScopes لأننا نريد كل الفروع هنا
            $subscriptions = StudentSubscription::query()
                ->withoutGlobalScopes()
                ->with(['student'])
                ->whereDate('remaining_due_date', '<=', $twoDays)
                ->whereDate('remaining_due_date', '>=', $today)
                ->where('remaining_amount', '>', 0)
                ->get();

            foreach ($subscriptions as $subscription) {
                $branchId    = $subscription->branch_id;
                $studentName = $subscription->student?->full_name ?? 'طالب';
                $remaining   = number_format((float) $subscription->remaining_amount, 2) . ' ج';
                $dueDate     = $subscription->remaining_due_date?->format('Y-m-d') ?? '-';

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
                        'title'     => "تنبيه سداد: {$studentName}",
                        'message'   => "الطالب {$studentName} لديه مبلغ متبقي {$remaining} يستحق السداد بتاريخ {$dueDate}.",
                        'data'      => [
                            'subscription_id' => $subscription->id,
                            'student_id'      => $subscription->student_id,
                            'student_name'    => $studentName,
                            'remaining'       => $remaining,
                            'due_date'        => $dueDate,
                        ],
                        'is_read' => false,
                    ]);
                }
            }

            // احفظ في Cache حتى نهاية اليوم لتجنب إعادة الفحص
            Cache::put($cacheKey, true, now()->endOfDay());

        } catch (\Throwable) {
            // لا تكسر الصفحة إذا حدث خطأ في الفحص
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

