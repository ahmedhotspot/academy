<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Seeder;

class StudentEnrollmentsSeeder extends Seeder
{
    public function run(): void
    {
        $groups = Group::query()->where('status', 'active')->get();

        if ($groups->isEmpty()) {
            $this->command->warn('⚠️ لا توجد حلقات نشطة — تخطّي تسجيل الطلاب.');
            return;
        }

        // جمع الطلاب مرتبين حسب الفرع
        $studentsByBranch = Student::query()
            ->where('status', 'active')
            ->get()
            ->groupBy('branch_id');

        $enrolledCount = 0;

        foreach ($groups as $group) {
            $students = $studentsByBranch[$group->branch_id] ?? collect();

            if ($students->isEmpty()) {
                continue;
            }

            // حسب المطلوب: كل حلقة من 5 إلى 15 طالب
            $count = min($students->count(), rand(5, 15));

            // اختر طلاباً عشوائيين لهذه الحلقة
            $selected = $students->random($count);

            foreach ($selected as $student) {
                $exists = StudentEnrollment::query()
                    ->where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->exists();

                if (! $exists) {
                    StudentEnrollment::query()->create([
                        'student_id' => $student->id,
                        'group_id'   => $group->id,
                        'status'     => 'active',
                    ]);
                    $enrolledCount++;
                }
            }
        }

        $this->command->info("✅ تم تسجيل {$enrolledCount} طالب في الحلقات بنجاح.");
    }
}

