<?php

use App\Models\Branch;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeUserManager(): User
{
    $role = Role::findOrCreate('المشرف العام', 'web');

    foreach (['users.view', 'users.create', 'users.update', 'users.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role->syncPermissions(['users.view', 'users.create', 'users.update', 'users.delete']);

    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض صفحة فهرس المستخدمين للمستخدم المخول', function () {
    $user = makeUserManager();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('إدارة المستخدمين - الفهرس');
});

it('ينشئ مستخدمًا جديدًا مع الدور المحدد', function () {
    $user = makeUserManager();
    $branch = Branch::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.users.store'), [
        'name' => 'مستخدم تجريبي',
        'phone' => '0501234567',
        'email' => 'user-demo@academy.test',
        'username' => 'userdemo',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active',
        'role' => 'المعلم',
        'branch_id' => $branch->id,
    ]);

    $response
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'user-demo@academy.test',
        'name' => 'مستخدم تجريبي',
        'branch_id' => $branch->id,
    ]);
});

it('يعيد datatable المستخدمين بصيغة json', function () {
    $user = makeUserManager();

    $this->actingAs($user)
        ->getJson(route('admin.users.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
});

