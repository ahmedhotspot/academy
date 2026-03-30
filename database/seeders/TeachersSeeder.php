<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeachersSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::query()->pluck('id', 'name');

        $cairo      = $branches['فرع القاهرة']     ?? null;
        $giza       = $branches['فرع الجيزة']      ?? null;
        $alexandria = $branches['فرع الإسكندرية'] ?? null;

        // ---------------------------------------------------------
        // معلمون ومعلمات — 14 كادر تعليمي
        // ---------------------------------------------------------
        $teachers = [
            // فرع القاهرة — 5 معلمين
            ['name' => 'الشيخ عبد الرحمن السيد',   'phone' => '01001000101', 'branch_id' => $cairo],
            ['name' => 'الأستاذ محمد حسن الجمال',   'phone' => '01001000102', 'branch_id' => $cairo],
            ['name' => 'الأستاذة فاطمة علي نور',    'phone' => '01001000103', 'branch_id' => $cairo],
            ['name' => 'الأستاذة نورا إبراهيم عمر', 'phone' => '01001000104', 'branch_id' => $cairo],
            ['name' => 'الأستاذ يوسف أحمد رشاد',    'phone' => '01001000105', 'branch_id' => $cairo],

            // فرع الجيزة — 5 معلمين
            ['name' => 'الشيخ عمر سعيد الدوسري',   'phone' => '01001000201', 'branch_id' => $giza],
            ['name' => 'الأستاذة مريم خالد السبيعي','phone' => '01001000202', 'branch_id' => $giza],
            ['name' => 'الأستاذ حسام الدين طاهر',   'phone' => '01001000203', 'branch_id' => $giza],
            ['name' => 'الأستاذة سارة وليد منصور',  'phone' => '01001000204', 'branch_id' => $giza],
            ['name' => 'الأستاذ زياد رامي عثمان',   'phone' => '01001000205', 'branch_id' => $giza],

            // فرع الإسكندرية — 4 معلمين
            ['name' => 'الشيخ صالح ناصر الغامدي',   'phone' => '01001000301', 'branch_id' => $alexandria],
            ['name' => 'الأستاذة آمال سمير الفقي',  'phone' => '01001000302', 'branch_id' => $alexandria],
            ['name' => 'الأستاذ بلال محمود حجاج',   'phone' => '01001000303', 'branch_id' => $alexandria],
            ['name' => 'الأستاذة هبة الله أنور',     'phone' => '01001000304', 'branch_id' => $alexandria],
        ];

        $index = 1;
        foreach ($teachers as $data) {
            $emailIndex = str_pad($index, 2, '0', STR_PAD_LEFT);
            $user = User::query()->updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name'      => $data['name'],
                    'email'     => "teacher{$emailIndex}@academy.test",
                    'username'  => "teacher{$emailIndex}",
                    'password'  => Hash::make('123456'),
                    'branch_id' => $data['branch_id'],
                    'status'    => 'active',
                ]
            );
            $user->syncRoles(['المعلم']);
            $index++;
        }

        $this->command->info('✅ تم إنشاء ' . count($teachers) . ' معلم/معلمة بنجاح.');
    }
}

