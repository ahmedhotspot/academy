<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $cairoBranch = Branch::query()->where('name', 'فرع القاهرة')->first();

        $users = [
            [
                'name' => 'المشرف العام',
                'email' => 'admin@academy.test',
                'phone' => '01010000001',
                'username' => 'admin',
                'branch_id' => null,
                'role' => 'المشرف العام',
            ],
            [
                'name' => 'السكرتيرة',
                'email' => 'secretary@academy.test',
                'phone' => '01010000002',
                'username' => 'secretary',
                'branch_id' => $cairoBranch?->id,
                'role' => 'السكرتيرة',
            ],
            [
                'name' => 'المعلم',
                'email' => 'teacher@academy.test',
                'phone' => '01010000003',
                'username' => 'teacher',
                'branch_id' => $cairoBranch?->id,
                'role' => 'المعلم',
            ],
        ];

        foreach ($users as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'username' => $data['username'],
                    'password' => Hash::make('123456'),
                    'status' => 'active',
                    'branch_id' => $data['branch_id'],
                ]
            );

            $user->syncRoles([$data['role']]);
        }

        $this->command->info('تم إنشاء المستخدمين الأساسيين بنجاح.');
    }
}

