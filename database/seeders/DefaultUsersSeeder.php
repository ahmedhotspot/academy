<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@academy.test'],
            [
                'name' => 'المشرف العام',
                'phone' => '0500000001',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'branch_id' => null,
            ]
        );
        $superAdmin->syncRoles(['المشرف العام']);

        $secretary = User::query()->updateOrCreate(
            ['email' => 'secretary@academy.test'],
            [
                'name' => 'السكرتيرة',
                'phone' => '0500000002',
                'username' => 'secretary',
                'password' => Hash::make('password'),
                'status' => 'active',
                'branch_id' => null,
            ]
        );
        $secretary->syncRoles(['السكرتيرة']);

        $teacher = User::query()->updateOrCreate(
            ['email' => 'teacher@academy.test'],
            [
                'name' => 'المعلم',
                'phone' => '0500000003',
                'username' => 'teacher',
                'password' => Hash::make('password'),
                'status' => 'active',
                'branch_id' => null,
            ]
        );
        $teacher->syncRoles(['المعلم']);
    }
}

