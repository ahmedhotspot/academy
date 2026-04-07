<?php

namespace App\Providers;

use App\Services\Admin\NotificationAutoCheckService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // View Composer لكل صفحات لوحة التحكم
        // يعمل تلقائياً عند تحميل أي صفحة admin دون Cron Job
        View::composer('admin.layouts.master', function (\Illuminate\View\View $view) {
            $user = auth()->user();

            if (! $user) {
                $view->with('headerUnreadCount', 0);
                $view->with('headerRecentNotifications', collect());
                return;
            }

            /** @var NotificationAutoCheckService $service */
            $service = app(NotificationAutoCheckService::class);

            // فحص تلقائي للاشتراكات القريبة الاستحقاق وإنشاء الإشعارات
            $service->checkAndCreateDueReminders();

            $view->with('headerUnreadCount', $service->getUnreadCount($user));
            $view->with('headerRecentNotifications', $service->getRecentNotifications($user));
        });
    }
}
