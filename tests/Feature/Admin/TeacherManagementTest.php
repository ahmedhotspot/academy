<?php

use App\Models\Branch;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeTeacherManagerUser(): User
{
    $role = Role::findOrCreate('المشرف العام', 'web');

    foreach (['teachers.view', 'teachers.create', 'teachers.update', 'teachers.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role->syncPermissions(['teachers.view', 'teachers.create', 'teachers.update', 'teachers.delete']);
    Role::findOrCreate('المعلم', 'web');

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

function makeTeacherRecord(): User
{
    Role::findOrCreate('المعلم', 'web');

    $teacher = User::factory()->create([
        'branch_id' => Branch::factory()->create()->id,
    ]);
    $teacher->assignRole('المعلم');

    return $teacher;
}

it('يعرض صفحة فهرس المعلمين المستقلة', function () {
    $user = makeTeacherManagerUser();

    $this->actingAs($user)
        ->get(route('admin.teachers.index'))
        ->assertOk()
        ->assertSee('إدارة المعلمين - الفهرس');
});

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
        'whatsapp' => '0507654321',
        'username' => 'teacherdemo',
        'password' => '',
        'password_confirmation' => '',
        'status' => 'active',
        'branch_id' => $branch->id,
    ]);

    $response
        ->assertRedirect(route('admin.teachers.index'))
        ->assertSessionHas('success');

    $teacher = User::query()->where('username', 'teacherdemo')->first();

    expect($teacher)->not->toBeNull();
    expect($teacher->branch_id)->toBe($branch->id);
    expect($teacher->whatsapp)->toBe('0507654321');
    expect($teacher->hasRole('المعلم'))->toBeTrue();
});

it('يعرض بيانات datatable للمعلمين بصيغة json', function () {
    $user = makeTeacherManagerUser();
    makeTeacherRecord();

    $this->actingAs($user)
        ->getJson(route('admin.teachers.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
});

it('يعرض صفحة عرض المعلم', function () {
    $user = makeTeacherManagerUser();
    $teacher = makeTeacherRecord();

    $this->actingAs($user)
        ->get(route('admin.teachers.show', $teacher))
        ->assertOk()
        ->assertSee($teacher->name);
});

it('يعرض صفحة تعديل المعلم', function () {
    $user = makeTeacherManagerUser();
    $teacher = makeTeacherRecord();

    $this->actingAs($user)
        ->get(route('admin.teachers.edit', $teacher))
        ->assertOk()
        ->assertSee('تعديل المعلم');
});

it('يحدّث بيانات المعلم', function () {
    $user = makeTeacherManagerUser();
    $teacher = makeTeacherRecord();
    $newBranch = Branch::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.teachers.update', $teacher), [
        'name' => 'معلم بعد التعديل',
        'phone' => '0509999999',
        'whatsapp' => '0509999999',
        'username' => 'teacherupdated',
        'password' => '',
        'password_confirmation' => '',
        'status' => 'active',
        'branch_id' => $newBranch->id,
    ]);

    $response
        ->assertRedirect(route('admin.teachers.show', $teacher))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $teacher->id,
        'name' => 'معلم بعد التعديل',
        'whatsapp' => '0509999999',
        'branch_id' => $newBranch->id,
    ]);
});

it('يحذف المعلم', function () {
    $user = makeTeacherManagerUser();
    $teacher = makeTeacherRecord();

    $response = $this->actingAs($user)->delete(route('admin.teachers.destroy', $teacher));

    $response
        ->assertRedirect(route('admin.teachers.index'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('users', ['id' => $teacher->id]);
});

it('يسمح للسكرتيرة بالوصول إلى إدارة المعلمين', function () {
    $role = Role::findOrCreate('السكرتيرة', 'web');

    foreach (['teachers.view'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role->syncPermissions(['teachers.view']);

    $secretary = User::factory()->create();
    $secretary->assignRole('السكرتيرة');

    $this->actingAs($secretary)
        ->get(route('admin.teachers.index'))
        ->assertOk();
});

