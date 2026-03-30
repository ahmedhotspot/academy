<?php

namespace Database\Seeders;

use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TeacherAttendancesSeeder extends Seeder
{
    public function run(): void
    {
        $teacherRole = Role::findByName('المعلم', 'web');
        $teachers    = User::query()->role($teacherRole)->where('status', 'active')->get();

        if ($teachers->isEmpty()) {
            $this->command->warn('⚠️ لا يوجد معلمون — تخطّي حضور المعلمين.');
            return;
        }

        // حالات الحضور مع أوزان احتمالية
        $statuses = [
            'حاضر',  'حاضر',  'حاضر',  'حاضر',  'حاضر',  // 5/7 حاضر
            'غائب',                                          // 1/7 غائب
            'متأخر',                                         // 1/7 متأخر
        ];

        $today   = Carbon::today();
        $count   = 0;

        // آخر 30 يوم (لتوفير بيانات كافية للتقارير)
        for ($daysAgo = 29; $daysAgo >= 0; $daysAgo--) {
            $date = $today->copy()->subDays($daysAgo);

            // تخطّي الجمعة (عطلة رسمية)
            if ($date->isFriday()) {
                continue;
            }

            foreach ($teachers as $teacher) {
                $exists = TeacherAttendance::query()
                    ->where('teacher_id', $teacher->id)
                    ->where('attendance_date', $date->toDateString())
                    ->exists();

                if ($exists) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $notes  = match ($status) {
                    'غائب'  => 'غياب بعذر',
                    'متأخر' => 'تأخر ' . rand(10, 30) . ' دقيقة',
                    default => null,
                };

                TeacherAttendance::query()->create([
                    'teacher_id'      => $teacher->id,
                    'attendance_date' => $date->toDateString(),
                    'status'          => $status,
                    'notes'           => $notes,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ تم إنشاء {$count} سجل حضور للمعلمين (آخر 30 يوم).");
    }
}

