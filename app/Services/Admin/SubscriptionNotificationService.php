<?php

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\StudentSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionNotificationService
{
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
     * إنشاء إشعار للمديرين عندما يكون هناك اشتراك قريب الانتهاء
     */
    public function notifyApproachingExpiry(StudentSubscription $subscription): void
    {
        $daysRemaining = now()->startOfDay()->diffInDays($subscription->due_date->startOfDay(), false);

        $message = "الطالب {$subscription->student?->full_name} - الاشتراك ({$subscription->feePlan?->name}) سينتهي خلال {$daysRemaining} أيام";

        // إرسال إشعار للمديرين (users with admin role)
        $adminUsers = User::query()
            ->whereHas('roles', fn(Builder $q) => $q->where('name', 'admin'))
            ->where('branch_id', $subscription->branch_id)
            ->get();

        foreach ($adminUsers as $admin) {
            $this->createNotification(
                user: $admin,
                type: 'subscription_approaching',
                title: '⏰ اشتراك قريب الانتهاء',
                message: $message,
                data: [
                    'subscription_id' => $subscription->id,
                    'student_id' => $subscription->student_id,
                    'days_remaining' => $daysRemaining,
                    'due_date' => $subscription->due_date->format('Y-m-d'),
                ],
                branchId: $subscription->branch_id
            );
        }
    }

    /**
     * إنشاء إشعار للمديرين عندما ينتهي الاشتراك
     */
    public function notifyExpired(StudentSubscription $subscription): void
    {
        $daysOverdue = now()->startOfDay()->diffInDays($subscription->due_date->startOfDay());

        $message = "الطالب {$subscription->student?->full_name} - الاشتراك ({$subscription->feePlan?->name}) انتهى منذ {$daysOverdue} أيام";

        // إرسال إشعار للمديرين
        $adminUsers = User::query()
            ->whereHas('roles', fn(Builder $q) => $q->where('name', 'admin'))
            ->where('branch_id', $subscription->branch_id)
            ->get();

        foreach ($adminUsers as $admin) {
            $this->createNotification(
                user: $admin,
                type: 'subscription_expired',
                title: '🔔 اشتراك منتهي',
                message: $message,
                data: [
                    'subscription_id' => $subscription->id,
                    'student_id' => $subscription->student_id,
                    'days_overdue' => $daysOverdue,
                    'expired_date' => $subscription->due_date->format('Y-m-d'),
                ],
                branchId: $subscription->branch_id
            );
        }
    }

    /**
     * تنظيف الإشعارات القديمة للاشتراك (لتجنب التكرار)
     */
    public function clearOldSubscriptionNotifications(StudentSubscription $subscription, string $type): void
    {
        Notification::query()
            ->where('data->subscription_id', $subscription->id)
            ->where('type', $type)
            ->where('created_at', '<', now()->subHours(1))
            ->delete();
    }

    /**
     * إنشاء إشعار في قاعدة البيانات
     */
    private function createNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data,
        int $branchId
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'branch_id' => $branchId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
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

