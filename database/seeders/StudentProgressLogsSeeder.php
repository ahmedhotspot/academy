<?php

namespace Database\Seeders;

use App\Models\StudentEnrollment;
use App\Models\StudentProgressLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentProgressLogsSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = StudentEnrollment::query()
            ->where('status', 'active')
            ->with(['group'])
            ->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('⚠️ لا توجد تسجيلات — تخطّي سجلات التقدم.');
            return;
        }

        $memorizationAmounts = [
            'ربع صفحة', 'نصف صفحة', 'صفحة كاملة', 'صفحة وربع',
            'صفحة ونصف', 'صفحتان', '5 آيات', '10 آيات',
            'ثمن جزء', 'ربع جزء',
        ];

        $revisionAmounts = [
            'نصف صفحة', 'صفحة', 'صفحتان', 'ثلاث صفحات', 'نصف جزء',
            'جزء كامل', 'ربع جزء', 'عشر آيات', 'ربع حزب',
        ];

        $evalWeights = ['ممتاز', 'ممتاز', 'جيد جداً', 'جيد جداً', 'جيد', 'مقبول', 'ضعيف'];

        $masteryLevels = ['ممتاز', 'جيد جداً', 'جيد', 'مقبول', 'ضعيف'];
        $commitments   = ['ملتزم', 'ملتزم', 'ملتزم', 'متأخر'];

        $repeatedMistakes = [
            null,
            'مد الطبيعي',
            'الإدغام',
            'الإخفاء',
            'قلقلة القاف',
            'التفخيم والترقيق',
            'الغنة',
            'المد المتصل',
            null, null,
        ];

        $today = Carbon::today();
        $count = 0;

        foreach ($enrollments as $enrollment) {
            // 3 - 5 سجلات لكل تسجيل
            $logsCount = rand(3, 5);

            for ($i = 0; $i < $logsCount; $i++) {
                $daysAgo      = ($i * 4) + rand(0, 3);
                $progressDate = $today->copy()->subDays($daysAgo);

                if ($progressDate->isFriday()) {
                    $progressDate->subDay();
                }

                StudentProgressLog::query()->create([
                    'student_id'          => $enrollment->student_id,
                    'group_id'            => $enrollment->group_id,
                    'teacher_id'          => $enrollment->group?->teacher_id,
                    'progress_date'       => $progressDate->toDateString(),
                    'memorization_amount' => $memorizationAmounts[array_rand($memorizationAmounts)],
                    'revision_amount'     => $revisionAmounts[array_rand($revisionAmounts)],
                    'tajweed_evaluation'  => $evalWeights[array_rand($evalWeights)],
                    'tadabbur_evaluation' => $evalWeights[array_rand($evalWeights)],
                    'repeated_mistakes'   => $repeatedMistakes[array_rand($repeatedMistakes)],
                    'mastery_level'       => $masteryLevels[array_rand($masteryLevels)],
                    'commitment_status'   => $commitments[array_rand($commitments)],
                    'notes'               => null,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ تم إنشاء {$count} سجل تقدم دراسي بنجاح.");
    }
}

