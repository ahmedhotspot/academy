<?php

use App\Models\Guardian;
use App\Models\Branch;
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
    $branch = Branch::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.guardians.store'), [
        'branch_id' => $branch->id,
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
        'branch_id' => $branch->id,
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
                ['branch', 'full_name', 'phone', 'whatsapp', 'students_count', 'status'],
            ],
        ]);
});

it('يعرض للسكرتيرة أولياء أمور فرعها فقط', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    $role = Role::findOrCreate('السكرتيرة', 'web');
    Permission::findOrCreate('guardians.view', 'web');
    $role->syncPermissions(['guardians.view']);

    $secretary = User::factory()->create(['branch_id' => $branchA->id]);
    $secretary->assignRole('السكرتيرة');

    $visibleGuardian = Guardian::factory()->create(['branch_id' => $branchA->id]);
    Guardian::factory()->create(['branch_id' => $branchB->id]);

    $response = $this->actingAs($secretary)
        ->getJson(route('admin.guardians.datatable', ['draw' => 1]))
        ->assertOk();

    $response->assertJsonPath('recordsTotal', 1);
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $visibleGuardian->id);
});

