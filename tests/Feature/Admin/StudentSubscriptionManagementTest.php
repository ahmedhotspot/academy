<?php

use App\Models\Branch;
use App\Models\FeePlan;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeStudentSubscriptionManager(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['student-subscriptions.view', 'student-subscriptions.create', 'student-subscriptions.update', 'student-subscriptions.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['student-subscriptions.view', 'student-subscriptions.create', 'student-subscriptions.update', 'student-subscriptions.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

function makeSubscriptionPrerequisites(): array
{
    $branch = Branch::factory()->create();
    $student = Student::factory()->create([
        'branch_id' => $branch->id,
        'status' => 'active',
    ]);

    $feePlan = FeePlan::query()->create([
        'name' => 'الخطة الشهرية',
        'payment_cycle' => 'شهري',
        'amount' => 300,
        'has_sisters_discount' => false,
        'status' => 'active',
    ]);

    return compact('branch', 'student', 'feePlan');
}

it('يعرض حقل طريقة الدفع في نموذج إضافة الاشتراك', function () {
    $user = makeStudentSubscriptionManager();
    makeSubscriptionPrerequisites();

    $this->actingAs($user)
        ->get(route('admin.student-subscriptions.create'))
        ->assertOk()
        ->assertSee('طريقة الدفع')
        ->assertSee('نقدي')
        ->assertSee('انستاباي');
});

it('ينشئ اشتراكا جديدا مع طريقة الدفع المختارة', function () {
    $user = makeStudentSubscriptionManager();
    ['student' => $student, 'feePlan' => $feePlan] = makeSubscriptionPrerequisites();

    $response = $this->actingAs($user)->post(route('admin.student-subscriptions.store'), [
        'student_id' => $student->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 300,
        'discount_amount' => 0,
        'paid_amount' => 100,
        'status' => 'نشط',
        'payment_method' => 'instapay',
        'start_date' => '2026-04-01',
        'due_date' => '2026-05-01',
        'remaining_due_date' => '2026-05-01',
    ]);

    $subscription = StudentSubscription::query()->latest('id')->first();

    $response
        ->assertRedirect(route('admin.student-subscriptions.show', $subscription))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('student_subscriptions', [
        'id' => $subscription->id,
        'payment_method' => 'instapay',
    ]);

    $this->actingAs($user)
        ->get(route('admin.student-subscriptions.show', $subscription))
        ->assertOk()
        ->assertSee('انستاباي');
});

it('يحدث طريقة الدفع للاشتراك', function () {
    $user = makeStudentSubscriptionManager();
    ['branch' => $branch, 'student' => $student, 'feePlan' => $feePlan] = makeSubscriptionPrerequisites();

    $subscription = StudentSubscription::query()->create([
        'branch_id' => $branch->id,
        'student_id' => $student->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 300,
        'discount_amount' => 0,
        'final_amount' => 300,
        'paid_amount' => 0,
        'remaining_amount' => 300,
        'status' => 'نشط',
        'payment_method' => 'cash',
        'start_date' => '2026-04-01',
        'due_date' => '2026-05-01',
        'remaining_due_date' => '2026-05-01',
    ]);

    $response = $this->actingAs($user)->put(route('admin.student-subscriptions.update', $subscription), [
        'student_id' => $student->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 300,
        'discount_amount' => 0,
        'paid_amount' => 0,
        'status' => 'نشط',
        'payment_method' => 'instapay',
        'start_date' => '2026-04-01',
        'due_date' => '2026-05-01',
        'remaining_due_date' => '2026-05-01',
    ]);

    $response
        ->assertRedirect(route('admin.student-subscriptions.show', $subscription))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('student_subscriptions', [
        'id' => $subscription->id,
        'payment_method' => 'instapay',
    ]);
});

