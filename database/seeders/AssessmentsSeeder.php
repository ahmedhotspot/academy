<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssessmentsSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = StudentEnrollment::query()
            ->where('status', 'active')
            ->with(['group'])
            ->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('⚠️ لا توجد تسجيلات — تخطّي الاختبارات.');
            return;
        }

        $types   = ['أسبوعي', 'أسبوعي', 'شهري', 'ختم جزء'];
        $today   = Carbon::today();
        $count   = 0;

        foreach ($enrollments as $enrollment) {
            // 1 - 3 اختبارات لكل طالب
            $numAssessments = rand(1, 3);

            for ($i = 0; $i < $numAssessments; $i++) {
                $type        = $types[array_rand($types)];
                $daysAgo     = ($i * 7) + rand(0, 5);
                $assessDate  = $today->copy()->subDays($daysAgo);

                if ($assessDate->isFriday()) {
                    $assessDate->subDay();
                }

                // درجات واقعية
                $memorization = rand(60, 100) + (rand(0, 9) / 10);
                $tajweed      = rand(55, 100) + (rand(0, 9) / 10);
                $tadabbur     = ($type !== 'أسبوعي') ? round(rand(60, 100) + (rand(0, 9) / 10), 1) : null;

                Assessment::query()->create([
                    'student_id'          => $enrollment->student_id,
                    'group_id'            => $enrollment->group_id,
                    'teacher_id'          => $enrollment->group?->teacher_id,
                    'assessment_date'     => $assessDate->toDateString(),
                    'type'                => $type,
                    'memorization_result' => round($memorization, 1),
                    'tajweed_result'      => round($tajweed, 1),
                    'tadabbur_result'     => $tadabbur,
                    'notes'               => null,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ تم إنشاء {$count} اختبار بنجاح.");
    }
}

