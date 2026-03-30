<?php

namespace Database\Seeders;

use App\Models\FeePlan;
use App\Models\Student;
use App\Models\StudentSubscription;
use Illuminate\Database\Seeder;

class StudentSubscriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->get();
        $plans    = FeePlan::query()->where('status', 'active')->get();

        if ($students->isEmpty() || $plans->isEmpty()) {
            $this->command->warn('⚠️ لا يوجد طلاب أو خطط رسوم.');
            return;
        }

        // توزيع الخطط بشكل واقعي
        $planWeights = [];
        foreach ($plans as $plan) {
            $weight = match ($plan->payment_cycle) {
                'شهري'       => 5,
                'نصف شهري'  => 2,
                'أسبوعي'     => 2,
                'بالحلقة'    => 1,
                default       => 1,
            };
            for ($i = 0; $i < $weight; $i++) {
                $planWeights[] = $plan;
            }
        }

        // حالات الاشتراك مع أوزان
        $statusPool = [
            'نشط',    'نشط',    'نشط',    'نشط',    'نشط',    // 5/10 نشط
            'متأخر',  'متأخر',  'متأخر',                       // 3/10 متأخر
            'موقوف',                                             // 1/10 موقوف
            'مكتمل',                                             // 1/10 مكتمل
        ];

        $count = 0;
        foreach ($students as $student) {
            // تخطّي إذا كان لديه اشتراك بالفعل
            $exists = StudentSubscription::query()
                ->where('student_id', $student->id)
                ->exists();

            if ($exists) {
                continue;
            }

            /** @var FeePlan $plan */
            $plan   = $planWeights[array_rand($planWeights)];
            $status = $statusPool[array_rand($statusPool)];

            // خصم الأخوات — 10% إذا كانت الخطة تدعم ذلك
            $discount = 0;
            if ($plan->has_sisters_discount && rand(0, 4) === 0) {
                $discount = round($plan->amount * 0.10, 2);
            }

            $finalAmount = $plan->amount - $discount;

            // حساب المبلغ المدفوع والمتبقي حسب الحالة
            $paidAmount = match ($status) {
                'نشط'    => (float) $finalAmount,
                'متأخر'  => (float) round($finalAmount * rand(0, 70) / 100, 2),
                'موقوف'  => (float) round($finalAmount * rand(30, 80) / 100, 2),
                'مكتمل'  => (float) $finalAmount,
                default  => (float) $finalAmount,
            };

            $remaining = round($finalAmount - $paidAmount, 2);
            if ($remaining < 0) {
                $remaining = 0;
            }

            StudentSubscription::query()->create([
                'student_id'       => $student->id,
                'fee_plan_id'      => $plan->id,
                'amount'           => $plan->amount,
                'discount_amount'  => $discount,
                'final_amount'     => $finalAmount,
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remaining,
                'status'           => $status,
            ]);
            $count++;
        }

        $this->command->info("✅ تم إنشاء {$count} اشتراك طالب بنجاح.");
    }
}

