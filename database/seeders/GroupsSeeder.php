<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Group;
use App\Models\StudyLevel;
use App\Models\StudyTrack;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class GroupsSeeder extends Seeder
{
    public function run(): void
    {
        $cairo      = Branch::query()->where('name', 'فرع القاهرة')->first();
        $giza       = Branch::query()->where('name', 'فرع الجيزة')->first();
        $alexandria = Branch::query()->where('name', 'فرع الإسكندرية')->first();

        $teacherRole = Role::findByName('المعلم', 'web');
        $teachers    = User::query()->role($teacherRole)->get()->groupBy('branch_id');

        $levels = StudyLevel::query()->pluck('id', 'name');
        $tracks = StudyTrack::query()->pluck('id', 'name');

        $arabicTrack  = $tracks['عربي']   ?? null;
        $ajamTrack    = $tracks['أعجمي']  ?? null;
        $beginner     = $levels['مبتدئ']  ?? null;
        $intermediate = $levels['متوسط']  ?? null;
        $advanced     = $levels['متقدم']  ?? null;
        $ijaza        = $levels['إجازة'] ?? ($levels['إجازات'] ?? null);

        // دالة مساعدة لجلب معلم من فرع
        $getTeacher = function (?int $branchId, int $offset = 0) use ($teachers): ?int {
            if (! $branchId) {
                return null;
            }
            $list = $teachers[$branchId] ?? collect();
            if ($list->isEmpty()) {
                return null;
            }
            return $list[$offset % $list->count()]?->id;
        };

        $groups = [
            // ------ فرع القاهرة ------
            [
                'name'           => 'حلقة الفجر للمبتدئين',
                'branch_id'      => $cairo?->id,
                'teacher_id'     => $getTeacher($cairo?->id, 0),
                'study_level_id' => $beginner,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة المتوسطين — صباحية',
                'branch_id'      => $cairo?->id,
                'teacher_id'     => $getTeacher($cairo?->id, 1),
                'study_level_id' => $intermediate,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة المتقدمين',
                'branch_id'      => $cairo?->id,
                'teacher_id'     => $getTeacher($cairo?->id, 2),
                'study_level_id' => $advanced,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة الإجازات — تلاوة',
                'branch_id'      => $cairo?->id,
                'teacher_id'     => $getTeacher($cairo?->id, 3),
                'study_level_id' => $ijaza,
                'study_track_id' => $arabicTrack,
                'type'           => 'individual',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة الأعجمي للمبتدئين',
                'branch_id'      => $cairo?->id,
                'teacher_id'     => $getTeacher($cairo?->id, 4),
                'study_level_id' => $beginner,
                'study_track_id' => $ajamTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],

            // ------ فرع الجيزة ------
            [
                'name'           => 'حلقة المبتدئين — مساء',
                'branch_id'      => $giza?->id,
                'teacher_id'     => $getTeacher($giza?->id, 0),
                'study_level_id' => $beginner,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة المتوسطين — مساء',
                'branch_id'      => $giza?->id,
                'teacher_id'     => $getTeacher($giza?->id, 1),
                'study_level_id' => $intermediate,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة الأعجمي المتوسط',
                'branch_id'      => $giza?->id,
                'teacher_id'     => $getTeacher($giza?->id, 2),
                'study_level_id' => $intermediate,
                'study_track_id' => $ajamTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة المتقدمين — تجويد',
                'branch_id'      => $giza?->id,
                'teacher_id'     => $getTeacher($giza?->id, 3),
                'study_level_id' => $advanced,
                'study_track_id' => $arabicTrack,
                'type'           => 'individual',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة الأطفال الصغار',
                'branch_id'      => $giza?->id,
                'teacher_id'     => $getTeacher($giza?->id, 4),
                'study_level_id' => $beginner,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],

            // ------ فرع الإسكندرية ------
            [
                'name'           => 'حلقة الصباح للمبتدئين',
                'branch_id'      => $alexandria?->id,
                'teacher_id'     => $getTeacher($alexandria?->id, 0),
                'study_level_id' => $beginner,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة المتوسطين — إسكندرية',
                'branch_id'      => $alexandria?->id,
                'teacher_id'     => $getTeacher($alexandria?->id, 1),
                'study_level_id' => $intermediate,
                'study_track_id' => $arabicTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة الإجازة — إسكندرية',
                'branch_id'      => $alexandria?->id,
                'teacher_id'     => $getTeacher($alexandria?->id, 2),
                'study_level_id' => $ijaza,
                'study_track_id' => $arabicTrack,
                'type'           => 'individual',
                'schedule_type'  => 'daily',
                'status'         => 'active',
            ],
            [
                'name'           => 'حلقة الأعجمي — إسكندرية',
                'branch_id'      => $alexandria?->id,
                'teacher_id'     => $getTeacher($alexandria?->id, 3),
                'study_level_id' => $beginner,
                'study_track_id' => $ajamTrack,
                'type'           => 'group',
                'schedule_type'  => 'weekly',
                'status'         => 'active',
            ],
        ];

        foreach ($groups as $data) {
            if (! $data['branch_id'] || ! $data['teacher_id']) {
                continue;
            }
            Group::query()->updateOrCreate(
                ['name' => $data['name'], 'branch_id' => $data['branch_id']],
                $data
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($groups) . ' حلقة بنجاح.');
    }
}

