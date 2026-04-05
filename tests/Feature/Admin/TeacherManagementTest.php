<?php

use App\Models\Branch;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeTeacherManagerUser(): User
{
    $role = Role::findOrCreate('المشرف العام', 'web');

    foreach (['users.view', 'users.create', 'users.update', 'users.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role->syncPermissions(['users.view', 'users.create', 'users.update', 'users.delete']);
    Role::findOrCreate('المعلم', 'web');

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض صفحة إضافة المعلم المستقلة', function () {
    $user = makeTeacherManagerUser();

    $this->actingAs($user)
        ->get(route('admin.teachers.create'))
        ->assertOk()
        ->assertSee('إضافة معلم');
});

it('ينشئ معلمًا من الصفحة المستقلة', function () {
    $user = makeTeacherManagerUser();
    $branch = Branch::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.teachers.store'), [
        'name' => 'معلم تجريبي',
        'phone' => '0507654321',
        'email' => 'teacher-demo@academy.test',
        'username' => 'teacherdemo',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'status' => 'active',
        'branch_id' => $branch->id,
    ]);

    $response
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $teacher = User::query()->where('email', 'teacher-demo@academy.test')->first();

    expect($teacher)->not->toBeNull();
    expect($teacher->branch_id)->toBe($branch->id);
    expect($teacher->hasRole('المعلم'))->toBeTrue();
});

