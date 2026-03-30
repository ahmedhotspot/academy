<?php

namespace Database\Seeders;

use App\Models\StudentEnrollment;
use App\Models\StudentProgressLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentAttendancesSeeder extends Seeder
{
    public function run(): void
    {
        // لا يوجد جدول مستقل لحضور الطلاب في المخطط الحالي.
        // لذلك نعتمد على سجلات المتابعة اليومية كمؤشر حضور للـ Dashboard.
        $enrollments = StudentEnrollment::query()
            ->where('status', 'active')
            ->with('group')
            ->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('لا توجد تسجيلات طلاب نشطة لإنشاء حضور الطلاب.');
            return;
        }

        $memorizationAmounts = ['5 آيات', '10 آيات', 'ربع صفحة', 'نصف صفحة', 'صفحة'];
        $revisionAmounts = ['ربع صفحة', 'نصف صفحة', 'صفحة'];
        $evaluationPool = ['ممتاز', 'جيد جداً', 'جيد', 'مقبول'];

        $createdCount = 0;
        $today = Carbon::today();

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = $today->copy()->subDays($daysAgo);

            if ($date->isFriday()) {
                continue;
            }

            foreach ($enrollments as $enrollment) {
                // محاكاة الحضور: 82% حاضر، 10% متأخر، 8% غائب
                $chance = random_int(1, 100);

                if ($chance <= 8) {
                    // غائب: لا ننشئ سجل متابعة في هذا اليوم
                    continue;
                }

                $commitment = $chance <= 18 ? 'متأخر' : 'ملتزم';

                $exists = StudentProgressLog::query()
                    ->where('student_id', $enrollment->student_id)
                    ->whereDate('progress_date', $date->toDateString())
                    ->exists();

                if ($exists) {
                    continue;
                }

                StudentProgressLog::query()->create([
                    'student_id' => $enrollment->student_id,
                    'group_id' => $enrollment->group_id,
                    'teacher_id' => $enrollment->group?->teacher_id,
                    'progress_date' => $date->toDateString(),
                    'memorization_amount' => $memorizationAmounts[array_rand($memorizationAmounts)],
                    'revision_amount' => $revisionAmounts[array_rand($revisionAmounts)],
                    'tajweed_evaluation' => $evaluationPool[array_rand($evaluationPool)],
                    'tadabbur_evaluation' => $evaluationPool[array_rand($evaluationPool)],
                    'repeated_mistakes' => null,
                    'mastery_level' => $evaluationPool[array_rand($evaluationPool)],
                    'commitment_status' => $commitment,
                    'notes' => $commitment === 'متأخر' ? 'تأخر عن بداية الحلقة' : null,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("تم إنشاء {$createdCount} سجل حضور طلاب (عبر المتابعة اليومية).");
    }
}

