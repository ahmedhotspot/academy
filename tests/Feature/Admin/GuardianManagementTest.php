<?php

use App\Models\Guardian;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeGuardianManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['guardians.view', 'guardians.create', 'guardians.update', 'guardians.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['guardians.view', 'guardians.create', 'guardians.update', 'guardians.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض صفحة فهرس أولياء الأمور للمستخدم المخول', function () {
    $user = makeGuardianManager();

    $this->actingAs($user)
        ->get(route('admin.guardians.index'))
        ->assertOk()
        ->assertSee('إدارة أولياء الأمور - الفهرس');
});

it('ينشئ ولي أمر جديدًا بنجاح', function () {
    $user = makeGuardianManager();

    $response = $this->actingAs($user)->post(route('admin.guardians.store'), [
        'full_name' => 'عبد الله أحمد',
        'phone' => '0508887776',
        'whatsapp' => '0508887776',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.guardians.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('guardians', [
        'full_name' => 'عبد الله أحمد',
        'phone' => '0508887776',
    ]);
});

it('يعيد بيانات datatable لأولياء الأمور بصيغة json', function () {
    $user = makeGuardianManager();
    Guardian::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.guardians.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['full_name', 'phone', 'whatsapp', 'students_count', 'status'],
            ],
        ]);
});

