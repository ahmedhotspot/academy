<?php

namespace App\Console\Commands;

use App\Models\StudentSubscription;
use Illuminate\Console\Command;

/**
 * أمر لمزامنة حالات الاشتراكات بناءً على المبلغ المتبقي وتاريخ الاستحقاق.
 *
 * مفيد لمرة واحدة لتصحيح البيانات القديمة التي كانت تُعيَّن فيها
 * الحالة دائماً كـ "نشط" بغض النظر عن انتهاء تاريخ الاستحقاق.
 *
 * الاستخدام:
 *   php artisan subscriptions:sync-statuses
 *   php artisan subscriptions:sync-statuses --dry-run
 */
class SyncSubscriptionStatuses extends Command
{
    protected $signature = 'subscriptions:sync-statuses
                            {--dry-run : فقط اعرض ما سيتغير دون تطبيق التغييرات}';

    protected $description = 'مزامنة حالات الاشتراكات — تصحيح "نشط" إلى "متأخر" لمن تجاوز تاريخ استحقاقه ولديه متبقٍ غير مسدَّد';

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('وضع المعاينة (dry-run): لن يتم تطبيق أي تغييرات فعلية.');
        }

        // اجلب الاشتراكات التي ليست "مكتمل" ولا "موقوف"
        // (هذه الحالات لا تحتاج إلى تصحيح تلقائي)
        $subscriptions = StudentSubscription::query()
            ->withoutGlobalScopes()
            ->whereNotIn('status', ['مكتمل', 'موقوف'])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('لا توجد اشتراكات تحتاج إلى فحص.');
            return self::SUCCESS;
        }

        $this->info("فحص {$subscriptions->count()} اشتراك ...");

        $updated   = 0;
        $unchanged = 0;

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        foreach ($subscriptions as $subscription) {
            $correctStatus = StudentSubscription::resolveFinancialStatus(
                (float) $subscription->remaining_amount,
                $subscription->due_date
            );

            if ($subscription->status !== $correctStatus) {
                $this->newLine();
                $this->line(sprintf(
                    '  #%d — %s | الحالة الحالية: <comment>%s</comment> ← الصحيحة: <info>%s</info> | due_date: %s | متبقي: %s',
                    $subscription->id,
                    $subscription->student?->full_name ?? 'غير معروف',
                    $subscription->status,
                    $correctStatus,
                    $subscription->due_date?->format('Y-m-d') ?? '—',
                    number_format((float) $subscription->remaining_amount, 2) . ' ج'
                ));

                if (! $isDryRun) {
                    $subscription->update(['status' => $correctStatus]);
                }

                $updated++;
            } else {
                $unchanged++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($isDryRun) {
            $this->warn("المعاينة: سيتم تحديث {$updated} اشتراك، و{$unchanged} اشتراك بدون تغيير.");
        } else {
            $this->info("✓ تم تحديث {$updated} اشتراك بنجاح. {$unchanged} اشتراك لم يحتج تعديلاً.");
        }

        return self::SUCCESS;
    }
}

