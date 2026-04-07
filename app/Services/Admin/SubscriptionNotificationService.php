<?php

namespace App\Services\Admin;

use App\Models\StudentSubscription;
use Carbon\Carbon;

/**
 * خدمة تنبيهات انتهاء الاشتراكات
 * تعتمد على NotificationAutoCheckService للإشعارات التلقائية
 */
class SubscriptionNotificationService
{
    public function __construct(
        private readonly NotificationAutoCheckService $notificationService
    ) {}

    /**
     * البحث عن الاشتراكات التي اقترب موعد استحقاقها (في خلال يومين)
     */
    public function getApproachingExpirySubscriptions(): \Illuminate\Database\Eloquent\Collection
    {
        $today = now()->startOfDay();
        $inTwoDays = $today->copy()->addDays(2)->endOfDay();

        return StudentSubscription::query()
            ->where('remaining_amount', '>', 0)
            ->where('status', '!=', 'موقوف')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$today, $inTwoDays])
            ->with(['student', 'feePlan'])
            ->get();
    }

    /**
     * البحث عن الاشتراكات المنتهية (تاريخ الاستحقاق في الماضي)
     */
    public function getExpiredSubscriptions(): \Illuminate\Database\Eloquent\Collection
    {
        $today = now()->startOfDay();

        return StudentSubscription::query()
            ->where('remaining_amount', '>', 0)
            ->where('status', '!=', 'موقوف')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->with(['student', 'feePlan'])
            ->get();
    }

    /**
     * إرسال إشعار فوري للاشتراك القريب من الانتهاء
     * (يستخدم NotificationAutoCheckService الموجودة بالفعل)
     */
    public function notifyApproachingExpiry(StudentSubscription $subscription): void
    {
        $subscription->loadMissing(['student', 'feePlan']);
        $this->notificationService->checkAndCreateDueReminderForSubscription($subscription);
    }

    /**
     * إرسال إشعار فوري للاشتراك المنتهي
     * (يستخدم NotificationAutoCheckService الموجودة بالفعل)
     */
    public function notifyExpired(StudentSubscription $subscription): void
    {
        $subscription->loadMissing(['student', 'feePlan']);
        $this->notificationService->checkAndCreateDueReminderForSubscription($subscription);
    }

    /**
     * إرسال إشعارات للاشتراكات القريبة والمنتهية دفعة واحدة
     */
    public function notifyAll(): void
    {
        $subscriptions = StudentSubscription::query()
            ->where('remaining_amount', '>', 0)
            ->where('status', '!=', 'موقوف')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->addDays(2))
            ->with(['student', 'feePlan'])
            ->get();

        foreach ($subscriptions as $subscription) {
            $this->notificationService->checkAndCreateDueReminderForSubscription($subscription);
        }
    }

    /**
     * الحصول على عدد الاشتراكات القريبة والمنتهية
     */
    public function getSummary(): array
    {
        return [
            'approaching_count' => $this->getApproachingExpirySubscriptions()->count(),
            'expired_count' => $this->getExpiredSubscriptions()->count(),
        ];
    }
}

