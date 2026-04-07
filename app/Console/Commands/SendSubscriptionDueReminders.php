<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\StudentSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSubscriptionDueReminders extends Command
{
    /**
     * اسم الأمر
     */
    protected $signature = 'subscriptions:send-reminders';

    /**
     * وصف الأمر
     */
    protected $description = 'إرسال تنبيهات للاشتراكات التي يقترب موعد سداد الباقي (قبل يومين)';

    public function handle(): int
    {
        $today    = Carbon::today();
        $twoDays  = $today->copy()->addDays(2);

        // الاشتراكات التي تاريخ سداد الباقي بعد يومين تماماً + لديها باقي
        $subscriptions = StudentSubscription::query()
            ->with(['student', 'feePlan'])
            ->whereDate('remaining_due_date', $twoDays)
            ->where('remaining_amount', '>', 0)
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('لا توجد اشتراكات تستحق تنبيهاً اليوم.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($subscriptions as $subscription) {
            // تحديد الفرع الخاص بالاشتراك
            $branchId = $subscription->branch_id;

            // المستخدمون الذين يجب إرسال الإشعار إليهم:
            // - المشرف العام (branch_id IS NULL)
            // - مستخدمو نفس الفرع (المشرفين والسكرتيرة)
            $userQuery = User::query()
                ->whereNull('deleted_at');

            if ($branchId) {
                // أرسل لمستخدمي الفرع + المشرفين العامين
                $userQuery->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhereNull('branch_id');
                });
            }
            // إن لم يكن للاشتراك فرع، أرسل للمشرف العام فقط

            $users = $userQuery->get();

            $studentName = $subscription->student?->full_name ?? 'طالب';
            $remaining   = number_format((float) $subscription->remaining_amount, 2) . ' ج';
            $dueDate     = $subscription->remaining_due_date?->format('Y-m-d') ?? '-';

            foreach ($users as $user) {
                // تجنب التكرار - لا ترسل إشعاراً للاشتراك نفسه إن كان موجوداً مسبقاً
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
                    'title'     => "تنبيه سداد اشتراك: {$studentName}",
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

                $count++;
            }
        }

        $this->info("تم إرسال {$count} إشعار لـ " . $subscriptions->count() . " اشتراك.");

        return self::SUCCESS;
    }
}

