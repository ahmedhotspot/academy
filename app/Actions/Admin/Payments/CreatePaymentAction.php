<?php

namespace App\Actions\Admin\Payments;

use App\Actions\BaseAction;
use App\Models\Payment;
use App\Models\StudentSubscription;
use Illuminate\Validation\ValidationException;

class CreatePaymentAction extends BaseAction
{
    public function handle(array $data): Payment
    {
        /** @var StudentSubscription $subscription */
        $subscription = StudentSubscription::query()->findOrFail($data['student_subscription_id']);

        if ((int) $subscription->student_id !== (int) $data['student_id']) {
            throw ValidationException::withMessages([
                'student_subscription_id' => 'الاشتراك المختار لا يتبع الطالب المحدد.',
            ]);
        }

        $remainingAmount = (float) $subscription->remaining_amount;
        $paymentAmount = (float) $data['amount'];

        if ($remainingAmount <= 0) {
            throw ValidationException::withMessages([
                'student_subscription_id' => 'هذا الاشتراك مكتمل السداد ولا يمكن إضافة دفعة جديدة.',
            ]);
        }

        if ($paymentAmount > $remainingAmount) {
            throw ValidationException::withMessages([
                'amount' => 'المبلغ المدخل أكبر من المتبقي على الاشتراك.',
            ]);
        }

        // إنشاء الدفعة
        $payment = Payment::query()->create([
            'student_id'               => $data['student_id'],
            'student_subscription_id'  => $data['student_subscription_id'],
            'payment_date'             => $data['payment_date'],
            'amount'                   => $data['amount'],
            'receipt_number'           => $this->generateReceiptNumber(),
            'notes'                    => $data['notes'] ?? null,
        ]);

        // تحديث الاشتراك: المبلغ المدفوع والمتبقي
        $newPaidAmount = $subscription->paid_amount + $payment->amount;
        $newRemainingAmount = max(0, $subscription->final_amount - $newPaidAmount);

        $subscription->update([
            'paid_amount'      => $newPaidAmount,
            'remaining_amount' => $newRemainingAmount,
            'status'           => $newRemainingAmount > 0 ? 'نشط' : 'مكتمل',
        ]);

        return $payment->fresh();
    }

    /**
     * توليد رقم إيصال فريد
     */
    private function generateReceiptNumber(): string
    {
        $date = now()->format('YmdHis');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return $date . $random;
    }
}

