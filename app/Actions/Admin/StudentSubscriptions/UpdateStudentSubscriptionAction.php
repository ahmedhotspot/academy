<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\FeePlan;
use App\Models\Payment;
use App\Models\StudentSubscription;
use App\Services\Admin\NotificationAutoCheckService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class UpdateStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): StudentSubscription
    {
        /** @var StudentSubscription $subscription */
        $subscription = $data['subscription'];

        // إجمالي المدفوع الفعلي من جدول الدفعات
        $recordedPaidAmount = (float) Payment::query()
            ->where('student_subscription_id', $subscription->id)
            ->sum('amount');

        $discountAmount  = $data['discount_amount'] ?? 0;
        $finalAmount     = $data['amount'] - $discountAmount;
        $paidAmount      = (float) ($data['paid_amount'] ?? 0);

        // لا تسمح بأن يصبح المدفوع أقل من الدفعات المسجلة فعلياً
        if ($paidAmount + 0.0001 < $recordedPaidAmount) {
            throw ValidationException::withMessages([
                'paid_amount' => 'لا يمكن تقليل المدفوع عن إجمالي الدفعات المسجلة بالفعل.',
            ]);
        }

        // أي زيادة في paid_amount تُسجل كدفعة جديدة تلقائياً
        $newPaymentAmount = max(0, $paidAmount - $recordedPaidAmount);
        if ($newPaymentAmount > 0) {
            Payment::query()->create([
                'student_id'              => $subscription->student_id,
                'student_subscription_id' => $subscription->id,
                'payment_date'            => now()->toDateString(),
                'amount'                  => $newPaymentAmount,
                'receipt_number'          => now()->format('YmdHis') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'notes'                   => 'دفعة مسجلة تلقائياً من تعديل الاشتراك',
            ]);
        }

        // استخدم الإجمالي بعد إضافة أي دفعة جديدة لضمان تطابق الاشتراك مع دفتر الدفعات
        $paidAmount = $recordedPaidAmount + $newPaymentAmount;
        $remainingAmount = max(0, $finalAmount - $paidAmount);

        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])
            : $subscription->start_date ?? now();

        $dueDate = ! empty($data['due_date'])
            ? Carbon::parse($data['due_date'])
            : $this->resolveDueDate($data['fee_plan_id'], $startDate);

        $remainingDueDate = ! empty($data['remaining_due_date'])
            ? Carbon::parse($data['remaining_due_date'])
            : $dueDate;

        $subscription->update([
            'student_id'         => $data['student_id'],
            'fee_plan_id'        => $data['fee_plan_id'],
            'amount'             => $data['amount'],
            'discount_amount'    => $discountAmount,
            'final_amount'       => $finalAmount,
            'paid_amount'        => $paidAmount,
            'remaining_amount'   => $remainingAmount,
            'status'             => $data['status'] ?? StudentSubscription::resolveFinancialStatus(
                (float) $remainingAmount,
                $dueDate
            ),
            'payment_method'     => $data['payment_method'],
            'start_date'         => $startDate,
            'due_date'           => $dueDate,
            'remaining_due_date' => $remainingDueDate,
        ]);

        // إعادة فحص الإشعارات فوراً بعد التعديل بدلاً من انتظار cache الساعة
        Cache::forget('subscription_reminders_checked_' . now()->format('Y-m-d-H'));

        // إنشاء إشعار فوري للاشتراك المعدّل إذا أصبح مستحقاً/متأخراً
        app(NotificationAutoCheckService::class)
            ->checkAndCreateDueReminderForSubscription($subscription->fresh(['student']));

        return $subscription->fresh();
    }

    private function resolveDueDate(int $feePlanId, Carbon $startDate): ?Carbon
    {
        $feePlan = FeePlan::query()->find($feePlanId);
        if (! $feePlan) return null;
        return StudentSubscription::calculateDueDate($feePlan->payment_cycle, $startDate);
    }
}

