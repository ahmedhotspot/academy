<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

function makeAdminUser(): User
{
    Role::findOrCreate('المشرف العام', 'web');

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض لوحة التحكم الإدارية للمستخدم الموثق', function () {
    $user = makeAdminUser();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('لوحة التحكم');
});

it('يعرض صفحة قوالب الصفحات index للمستخدم الموثق', function () {
    $user = makeAdminUser();

    $this->actingAs($user)
        ->get(route('admin.page-patterns.index'))
        ->assertOk()
        ->assertSee('قوالب الصفحات - الفهرس');
});

it('يعيد بيانات datatable بصيغة json', function () {
    $user = makeAdminUser();

    $this->actingAs($user)
        ->getJson(route('admin.page-patterns.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['id', 'title', 'type', 'status', 'created_at'],
            ],
        ]);
});

