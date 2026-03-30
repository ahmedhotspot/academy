<?php

use App\Models\StudyLevel;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeStudyLevelManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['study-levels.view', 'study-levels.create', 'study-levels.update', 'study-levels.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['study-levels.view', 'study-levels.create', 'study-levels.update', 'study-levels.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض صفحة فهرس المستويات للمستخدم المخول', function () {
    $user = makeStudyLevelManager();

    $this->actingAs($user)
        ->get(route('admin.study-levels.index'))
        ->assertOk()
        ->assertSee('إدارة المستويات - الفهرس');
});

it('ينشئ مستوى جديدًا بنجاح', function () {
    $user = makeStudyLevelManager();

    $response = $this->actingAs($user)->post(route('admin.study-levels.store'), [
        'name' => 'مستوى تجريبي',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.study-levels.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('study_levels', [
        'name' => 'مستوى تجريبي',
        'status' => 'active',
    ]);
});

it('يعيد بيانات datatable للمستويات بصيغة json', function () {
    $user = makeStudyLevelManager();
    StudyLevel::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.study-levels.datatable', ['draw' => 1]))
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

