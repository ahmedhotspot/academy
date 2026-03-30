<?php

use App\Models\StudyTrack;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeStudyTrackManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['study-tracks.view', 'study-tracks.create', 'study-tracks.update', 'study-tracks.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['study-tracks.view', 'study-tracks.create', 'study-tracks.update', 'study-tracks.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض صفحة فهرس المسارات للمستخدم المخول', function () {
    $user = makeStudyTrackManager();

    $this->actingAs($user)
        ->get(route('admin.study-tracks.index'))
        ->assertOk()
        ->assertSee('إدارة المسارات - الفهرس');
});

it('ينشئ مسارًا جديدًا بنجاح', function () {
    $user = makeStudyTrackManager();

    $response = $this->actingAs($user)->post(route('admin.study-tracks.store'), [
        'name' => 'مسار تجريبي',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.study-tracks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('study_tracks', [
        'name' => 'مسار تجريبي',
        'status' => 'active',
    ]);
});

it('يعيد بيانات datatable للمسارات بصيغة json', function () {
    $user = makeStudyTrackManager();
    StudyTrack::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.study-tracks.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['name', 'status', 'status_badge'],
            ],
        ]);
});

