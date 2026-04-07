<?php

namespace App\Actions\Admin\Payments;

use App\Actions\BaseAction;
use App\Models\Payment;
use App\Models\StudentSubscription;

class UpdatePaymentAction extends BaseAction
{
    public function handle(array $data): Payment
    {
        /** @var Payment $payment */
        $payment = $data['payment'];

        // الحصول على الاشتراك
        $subscription = $payment->subscription;
        $oldAmount = $payment->amount;

        // تحديث الدفعة
        $payment->update([
            'payment_date' => $data['payment_date'],
            'amount'       => $data['amount'],
            'notes'        => $data['notes'] ?? null,
        ]);

        // إعادة حساب الاشتراك
        $amountDifference = $data['amount'] - $oldAmount;
        $newPaidAmount = $subscription->paid_amount + $amountDifference;
        $newRemainingAmount = max(0, $subscription->final_amount - $newPaidAmount);

        $subscription->update([
            'paid_amount'      => $newPaidAmount,
            'remaining_amount' => $newRemainingAmount,
            'status'           => StudentSubscription::resolveFinancialStatus(
                (float) $newRemainingAmount,
                $subscription->due_date
            ),
        ]);

        return $payment->fresh();
    }
}

