<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// إرسال تنبيهات الاشتراكات التي يقترب موعد سدادها (كل يوم الساعة 8 صباحاً)
Schedule::command('subscriptions:send-reminders')->dailyAt('08:00');

// مزامنة حالات الاشتراكات (تصحيح "نشط" → "متأخر") كل يوم منتصف الليل
Schedule::command('subscriptions:sync-statuses')->dailyAt('00:00');

// إرسال إشعارات الاشتراكات القريبة والمنتهية (كل يوم الساعة 9 صباحاً و 3 مساءً)
Schedule::command('subscriptions:notify-expiry --all')->dailyAt('09:00');
Schedule::command('subscriptions:notify-expiry --all')->dailyAt('15:00');

