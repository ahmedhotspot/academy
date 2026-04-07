<?php

namespace App\Actions\Admin\Payments;

use App\Actions\BaseAction;
use App\Models\Payment;
use App\Models\StudentSubscription;

class DeletePaymentAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Payment $payment */
        $payment = $data['payment'];
        $subscription = $payment->subscription;
        $paymentAmount = $payment->amount;

        // حذف الدفعة
        $deleted = (bool) $payment->delete();

        if ($deleted) {
            // إعادة حساب الاشتراك
            $newPaidAmount = max(0, $subscription->paid_amount - $paymentAmount);
            $newRemainingAmount = max(0, $subscription->final_amount - $newPaidAmount);

            $subscription->update([
                'paid_amount'      => $newPaidAmount,
                'remaining_amount' => $newRemainingAmount,
                'status'           => StudentSubscription::resolveFinancialStatus(
                    (float) $newRemainingAmount,
                    $subscription->due_date
                ),
            ]);
        }

        return $deleted;
    }
}

