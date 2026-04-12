<?php

use App\Models\Branch;
use App\Models\Group;
use App\Models\StudyLevel;
use App\Models\StudyTrack;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeGroupManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['groups.view', 'groups.create', 'groups.update', 'groups.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['groups.view', 'groups.create', 'groups.update', 'groups.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

function makeBranchGroupManager(int $branchId, string $roleName = 'السكرتيرة'): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['groups.view', 'groups.create', 'groups.update', 'groups.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName($roleName, 'web');
    $role->syncPermissions(['groups.view', 'groups.create', 'groups.update', 'groups.delete']);

    $user = User::factory()->create(['branch_id' => $branchId]);
    $user->assignRole($roleName);

    return $user;
}

it('يعرض صفحة فهرس الحلقات للمستخدم المخول', function () {
    $user = makeGroupManager();

    $this->actingAs($user)
        ->get(route('admin.groups.index'))
        ->assertOk()
        ->assertSee('إدارة الحلقات - الفهرس');
});

it('ينشئ حلقة جديدة بنجاح', function () {
    $user = makeGroupManager();
    $branch = Branch::factory()->create();
    $teacher = User::factory()->create();
    $teacher->assignRole('المعلم');
    $studyLevel = StudyLevel::factory()->create();
    $studyTrack = StudyTrack::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.groups.store'), [
        'branch_id' => $branch->id,
        'teacher_id' => $teacher->id,
        'study_level_id' => $studyLevel->id,
        'study_track_id' => $studyTrack->id,
        'name' => 'حلقة الفجر',
        'type' => 'group',
        'schedule_type' => 'daily',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.groups.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('groups', [
        'name' => 'حلقة الفجر',
        'branch_id' => $branch->id,
    ]);
});

it('يعيد بيانات datatable للحلقات بصيغة json', function () {
    $user = makeGroupManager();
    Group::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.groups.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['name', 'branch', 'teacher', 'study_level', 'study_track', 'type', 'schedule_type', 'status'],
            ],
        ]);
});

it('يعرض في نموذج الحلقة معلمي فرع المستخدم فقط', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    $secretary = makeBranchGroupManager($branchA->id);

    $visibleTeacher = User::factory()->create([
        'branch_id' => $branchA->id,
        'name' => 'معلم الفرع الأول',
    ]);
    $visibleTeacher->assignRole('المعلم');

    $hiddenTeacher = User::factory()->create([
        'branch_id' => $branchB->id,
        'name' => 'معلم الفرع الثاني',
    ]);
    $hiddenTeacher->assignRole('المعلم');

    $this->actingAs($secretary)
        ->get(route('admin.groups.create'))
        ->assertOk()
        ->assertSee($visibleTeacher->name)
        ->assertDontSee($hiddenTeacher->name);
});

it('يرفض إنشاء حلقة بمعلم من فرع آخر', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();
    $studyLevel = StudyLevel::factory()->create();
    $studyTrack = StudyTrack::factory()->create();

    $secretary = makeBranchGroupManager($branchA->id);

    $foreignTeacher = User::factory()->create(['branch_id' => $branchB->id]);
    $foreignTeacher->assignRole('المعلم');

    $response = $this->actingAs($secretary)
        ->from(route('admin.groups.create'))
        ->post(route('admin.groups.store'), [
            'branch_id' => $branchA->id,
            'teacher_id' => $foreignTeacher->id,
            'study_level_id' => $studyLevel->id,
            'study_track_id' => $studyTrack->id,
            'name' => 'حلقة غير صحيحة',
            'type' => 'group',
            'schedule_type' => 'daily',
            'status' => 'active',
        ]);

    $response
        ->assertRedirect(route('admin.groups.create'))
        ->assertSessionHasErrors(['teacher_id']);

    $this->assertDatabaseMissing('groups', [
        'name' => 'حلقة غير صحيحة',
    ]);
});

