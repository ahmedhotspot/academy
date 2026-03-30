<?php

use App\Models\Branch;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\StudyLevel;
use App\Models\StudyTrack;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeGroupScheduleManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['groups.view', 'group-schedules.view', 'group-schedules.create', 'group-schedules.update', 'group-schedules.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['groups.view', 'group-schedules.view', 'group-schedules.create', 'group-schedules.update', 'group-schedules.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

function makeBaseGroup(): Group
{
    $branch = Branch::factory()->create();
    $teacher = User::factory()->create();
    $teacher->assignRole('المعلم');
    $studyLevel = StudyLevel::factory()->create();
    $studyTrack = StudyTrack::factory()->create();

    return Group::factory()->create([
        'branch_id' => $branch->id,
        'teacher_id' => $teacher->id,
        'study_level_id' => $studyLevel->id,
        'study_track_id' => $studyTrack->id,
    ]);
}

it('يعرض صفحة فهرس جداول الحلقات للمستخدم المخول', function () {
    $user = makeGroupScheduleManager();

    $this->actingAs($user)
        ->get(route('admin.group-schedules.index'))
        ->assertOk()
        ->assertSee('إدارة جداول الحلقات - الفهرس');
});

it('ينشئ جدول حلقة جديدًا بنجاح', function () {
    $user = makeGroupScheduleManager();
    $group = makeBaseGroup();

    $response = $this->actingAs($user)->post(route('admin.group-schedules.store'), [
        'group_id' => $group->id,
        'day_name' => 'الأحد',
        'start_time' => '16:00',
        'end_time' => '17:00',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.group-schedules.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('group_schedules', [
        'group_id' => $group->id,
        'day_name' => 'الأحد',
        'status' => 'active',
    ]);
});

it('يعرض جداول الحلقة داخل صفحة عرض الحلقة', function () {
    $user = makeGroupScheduleManager();
    $group = makeBaseGroup();

    GroupSchedule::factory()->create([
        'group_id' => $group->id,
        'day_name' => 'الاثنين',
        'start_time' => '18:00:00',
        'end_time' => '19:00:00',
    ]);

    $this->actingAs($user)
        ->get(route('admin.groups.show', $group))
        ->assertOk()
        ->assertSee('الاثنين')
        ->assertSee('18:00')
        ->assertSee('19:00');
});

