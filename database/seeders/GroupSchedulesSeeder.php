<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupSchedule;
use Illuminate\Database\Seeder;

class GroupSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        // جدول يومي: السبت إلى الأربعاء
        $dailyDays   = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء'];
        // جدول أسبوعي: ثلاثة أيام مختلفة
        $weeklyDays  = [
            ['السبت', 'الاثنين', 'الأربعاء'],
            ['الأحد', 'الثلاثاء', 'الخميس'],
            ['السبت', 'الثلاثاء', 'الخميس'],
        ];

        $timeSlots = [
            ['start' => '08:00:00', 'end' => '09:00:00'],
            ['start' => '09:30:00', 'end' => '10:30:00'],
            ['start' => '11:00:00', 'end' => '12:00:00'],
            ['start' => '15:00:00', 'end' => '16:00:00'],
            ['start' => '16:30:00', 'end' => '17:30:00'],
            ['start' => '18:00:00', 'end' => '19:00:00'],
            ['start' => '19:30:00', 'end' => '20:30:00'],
        ];

        $groups = Group::query()->get();
        $slotIndex = 0;

        foreach ($groups as $group) {
            // حذف الجداول القديمة لهذه الحلقة
            GroupSchedule::query()->where('group_id', $group->id)->delete();

            $slot = $timeSlots[$slotIndex % count($timeSlots)];

            if ($group->schedule_type === 'daily') {
                foreach ($dailyDays as $day) {
                    GroupSchedule::query()->create([
                        'group_id'   => $group->id,
                        'day_name'   => $day,
                        'start_time' => $slot['start'],
                        'end_time'   => $slot['end'],
                        'status'     => 'active',
                    ]);
                }
            } else {
                // أسبوعي — 3 أيام
                $days = $weeklyDays[$slotIndex % count($weeklyDays)];
                foreach ($days as $day) {
                    GroupSchedule::query()->create([
                        'group_id'   => $group->id,
                        'day_name'   => $day,
                        'start_time' => $slot['start'],
                        'end_time'   => $slot['end'],
                        'status'     => 'active',
                    ]);
                }
            }

            $slotIndex++;
        }

        $total = GroupSchedule::query()->count();
        $this->command->info("✅ تم إنشاء {$total} جدول دراسي بنجاح.");
    }
}

