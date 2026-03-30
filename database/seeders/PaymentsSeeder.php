<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\StudentSubscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = StudentSubscription::query()
            ->with('student')
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->command->warn('لا توجد اشتراكات طلاب لإنشاء المدفوعات.');
            return;
        }

        $receiptCounter = 1000;
        $paymentsCount = 0;

        foreach ($subscriptions as $subscription) {
            $finalAmount = (float) $subscription->final_amount;

            if ($finalAmount <= 0) {
                continue;
            }

            $scenario = random_int(1, 100);

            // تحديد سيناريو الدفع
            if ($scenario <= 55) {
                // دفع كامل
                $totalPaid = $finalAmount;
                $status = 'مكتمل';
            } elseif ($scenario <= 85) {
                // دفع جزئي
                $totalPaid = round($finalAmount * (random_int(30, 80) / 100), 2);
                $status = 'متأخر';
            } else {
                // لم يدفع
                $totalPaid = 0;
                $status = 'متأخر';
            }

            $remaining = round($finalAmount - $totalPaid, 2);
            if ($remaining < 0) {
                $remaining = 0;
            }

            // عدد الدفعات: كامل = 1-2 دفعة، جزئي = 1-3 دفعة
            $installments = $totalPaid <= 0 ? 0 : ($status === 'مكتمل' ? random_int(1, 2) : random_int(1, 3));

            $remainingToDistribute = $totalPaid;
            for ($i = 1; $i <= $installments; $i++) {
                $amount = $i === $installments
                    ? $remainingToDistribute
                    : round($totalPaid / $installments, 2);

                $remainingToDistribute = round($remainingToDistribute - $amount, 2);

                if ($amount <= 0) {
                    continue;
                }

                $paymentDate = Carbon::today()->subDays(random_int(0, 45));

                Payment::query()->create([
                    'student_id' => $subscription->student_id,
                    'student_subscription_id' => $subscription->id,
                    'payment_date' => $paymentDate->toDateString(),
                    'amount' => $amount,
                    'receipt_number' => 'RCPT-' . $paymentDate->format('Ymd') . '-' . $receiptCounter,
                    'notes' => $status === 'مكتمل' ? 'سداد كامل' : 'سداد جزئي',
                ]);

                $receiptCounter++;
                $paymentsCount++;
            }

            $subscription->update([
                'paid_amount' => $totalPaid,
                'remaining_amount' => $remaining,
                'status' => $remaining <= 0 ? 'مكتمل' : $status,
            ]);
        }

        $this->command->info("تم إنشاء {$paymentsCount} دفعة مالية بنجاح.");
    }
}

