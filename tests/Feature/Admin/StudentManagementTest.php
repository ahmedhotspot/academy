<?php

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeStudentManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['students.view', 'students.create', 'students.update', 'students.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['students.view', 'students.create', 'students.update', 'students.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

it('يعرض صفحة فهرس الطلاب للمستخدم المخول', function () {
    $user = makeStudentManager();

    $this->actingAs($user)
        ->get(route('admin.students.index'))
        ->assertOk()
        ->assertSee('إدارة الطلاب - الفهرس');
});

it('ينشئ طالبًا جديدًا بنجاح', function () {
    $user = makeStudentManager();
    $branch = Branch::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.students.store'), [
        'branch_id' => $branch->id,
        'student_code' => 'STD-1001',
        'guardian_mode' => 'none',
        'guardian_id' => null,
        'full_name' => 'أحمد محمد',
        'enrollment_date' => '2026-01-10',
        'birth_date' => '2014-03-05',
        'age' => 12,
        'nationality' => 'سعودي',
        'identity_number' => 'A1234567',
        'identity_expiry_date' => '',
        'gender' => 'male',
        'phone' => '0501234567',
        'whatsapp' => '0501234567',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.students.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('students', [
        'student_code' => 'STD-1001',
        'full_name' => 'أحمد محمد',
        'branch_id' => $branch->id,
    ]);
});

it('ينشئ طالبًا مع إنشاء ولي أمر جديد من نفس النموذج', function () {
    $user = makeStudentManager();
    $branch = Branch::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.students.store'), [
        'branch_id' => $branch->id,
        'student_code' => 'STD-1002',
        'guardian_mode' => 'new',
        'guardian_full_name' => 'ولي أمر جديد',
        'guardian_phone' => '0507776665',
        'guardian_whatsapp' => '0507776665',
        'full_name' => 'طالب جديد',
        'enrollment_date' => '2026-02-01',
        'birth_date' => '2013-08-19',
        'age' => 13,
        'nationality' => 'مصري',
        'identity_number' => 'P998877',
        'identity_expiry_date' => '2029-11-30',
        'gender' => 'male',
        'phone' => '0501112233',
        'whatsapp' => '0501112233',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.students.index'))
        ->assertSessionHas('success');

    $guardian = Guardian::query()->where('full_name', 'ولي أمر جديد')->first();

    expect($guardian)->not->toBeNull();

    $this->assertDatabaseHas('students', [
        'student_code' => 'STD-1002',
        'full_name' => 'طالب جديد',
        'guardian_id' => $guardian->id,
    ]);
});

it('يرفض إنشاء طالب بدون كود الطالب أو رقم الهوية', function () {
    $user = makeStudentManager();
    $branch = Branch::factory()->create();

    $response = $this->actingAs($user)->from(route('admin.students.create'))->post(route('admin.students.store'), [
        'branch_id' => $branch->id,
        'guardian_mode' => 'none',
        'guardian_id' => null,
        'full_name' => 'طالب بدون بيانات أساسية',
        'enrollment_date' => '2026-01-10',
        'birth_date' => '2014-03-05',
        'age' => 12,
        'nationality' => 'سعودي',
        'identity_number' => '',
        'identity_expiry_date' => '',
        'gender' => 'male',
        'phone' => '0501234567',
        'whatsapp' => '0501234567',
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.students.create'))
        ->assertSessionHasErrors(['student_code', 'identity_number'])
        ->assertSessionDoesntHaveErrors(['identity_expiry_date']);
});

it('يعيد بيانات datatable للطلاب بصيغة json', function () {
    $user = makeStudentManager();
    Student::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.students.datatable', ['draw' => 1]))
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['student_code', 'full_name', 'age', 'nationality', 'identity_number', 'phone', 'whatsapp', 'branch', 'status'],
            ],
        ]);
});

