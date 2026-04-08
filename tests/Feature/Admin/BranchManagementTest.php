<?php

use App\Models\Branch;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeBranchManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['branches.view', 'branches.create', 'branches.update', 'branches.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['branches.view', 'branches.create', 'branches.update', 'branches.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

function makeSecretaryForBranch(Branch $branch): User
{
    Role::findOrCreate('السكرتيرة', 'web');

    foreach (['branches.view'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('السكرتيرة', 'web');
    $role->syncPermissions(['branches.view']);

    $user = User::factory()->create([
        'branch_id' => $branch->id,
    ]);
    $user->assignRole('السكرتيرة');

    return $user;
}

it('يعرض صفحة فهرس الفروع للمستخدم المخول', function () {
    $user = makeBranchManager();

    $this->actingAs($user)
        ->get(route('admin.branches.index'))
        ->assertOk()
        ->assertSee('إدارة الفروع - الفهرس');
});

it('ينشئ فرعًا جديدًا بنجاح', function () {
    $user = makeBranchManager();

    $response = $this->actingAs($user)->post(route('admin.branches.store'), [
        'name' => 'فرع شمال المدينة',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.branches.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('branches', [
        'name' => 'فرع شمال المدينة',
        'status' => 'active',
    ]);
});

it('يعيد بيانات datatable للفروع بصيغة json', function () {
    $user = makeBranchManager();
    Branch::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.branches.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['id', 'name', 'status', 'status_badge', 'created_at'],
            ],
        ]);
});

it('يعرض في datatable للسكرتيرة فرعها فقط', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();
    $secretary = makeSecretaryForBranch($branchA);

    $this->actingAs($secretary)
        ->getJson(route('admin.branches.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('recordsFiltered', 1)
        ->assertJsonPath('data.0.id', $branchA->id)
        ->assertJsonMissing(['id' => $branchB->id]);
});

it('يمنع السكرتيرة من عرض تفاصيل فرع آخر', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();
    $secretary = makeSecretaryForBranch($branchA);

    $this->actingAs($secretary)
        ->get(route('admin.branches.show', $branchB))
        ->assertForbidden();
});

