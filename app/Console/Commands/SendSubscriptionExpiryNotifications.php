<?php

namespace App\Console\Commands;

use App\Services\Admin\SubscriptionNotificationService;
use Illuminate\Console\Command;

class SendSubscriptionExpiryNotifications extends Command
{
    protected $signature = 'subscriptions:notify-expiry {--approaching} {--expired} {--all}';

    protected $description = 'إرسال إشعارات للمديرين عن الاشتراكات القريبة والمنتهية';

    public function __construct(
        private readonly SubscriptionNotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $showApproaching = $this->option('approaching') || $this->option('all');
        $showExpired = $this->option('expired') || $this->option('all');

        // إذا لم يتم تحديد خيار، أرسل كلا النوعين
        if (! $showApproaching && ! $showExpired) {
            $showApproaching = true;
            $showExpired = true;
        }

        if ($showApproaching) {
            $this->handleApproachingSubscriptions();
        }

        if ($showExpired) {
            $this->handleExpiredSubscriptions();
        }

        $this->info('✓ تم إرسال الإشعارات بنجاح');
    }

    private function handleApproachingSubscriptions(): void
    {
        $subscriptions = $this->notificationService->getApproachingExpirySubscriptions();

        if ($subscriptions->isEmpty()) {
            $this->info('لا توجد اشتراكات قريبة الانتهاء');
            return;
        }

        $this->info("📢 إرسال إشعارات للاشتراكات القريبة ({$subscriptions->count()})...");

        foreach ($subscriptions as $subscription) {
            // تنظيف الإشعارات القديمة
            $this->notificationService->clearOldSubscriptionNotifications($subscription, 'subscription_approaching');

            // إرسال إشعار جديد
            $this->notificationService->notifyApproachingExpiry($subscription);

            $this->info("  ✓ تم إرسال إشعار للطالب: {$subscription->student?->full_name}");
        }
    }

    private function handleExpiredSubscriptions(): void
    {
        $subscriptions = $this->notificationService->getExpiredSubscriptions();

        if ($subscriptions->isEmpty()) {
            $this->info('لا توجد اشتراكات منتهية');
            return;
        }

        $this->info("⚠️ إرسال إشعارات للاشتراكات المنتهية ({$subscriptions->count()})...");

        foreach ($subscriptions as $subscription) {
            // تنظيف الإشعارات القديمة
            $this->notificationService->clearOldSubscriptionNotifications($subscription, 'subscription_expired');

            // إرسال إشعار جديد
            $this->notificationService->notifyExpired($subscription);

            $this->info("  ✓ تم إرسال إشعار للطالب: {$subscription->student?->full_name}");
        }
    }
}

