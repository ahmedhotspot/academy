<?php

use App\Actions\Admin\FeePlans\DeleteFeePlanAction;
use App\Models\Branch;
use App\Models\FeePlan;
use App\Models\Student;
use App\Models\StudentSubscription;

it('prevents deleting a fee plan when student subscriptions are linked', function () {
    $branch = Branch::factory()->create();
    $student = Student::factory()->create(['branch_id' => $branch->id]);

    $feePlan = FeePlan::query()->create([
        'name' => 'خطة لا تحذف',
        'payment_cycle' => 'شهري',
        'amount' => 300,
        'has_sisters_discount' => false,
        'status' => 'active',
    ]);

    StudentSubscription::query()->create([
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
        'start_date' => now()->toDateString(),
        'due_date' => now()->addMonth()->toDateString(),
        'remaining_due_date' => now()->addMonth()->toDateString(),
    ]);

    $deleted = app(DeleteFeePlanAction::class)->handle(['feePlan' => $feePlan]);

    expect($deleted)->toBeFalse();
    $this->assertDatabaseHas('fee_plans', ['id' => $feePlan->id]);
});

it('deletes a fee plan when there are no linked subscriptions', function () {
    $feePlan = FeePlan::query()->create([
        'name' => 'خطة قابلة للحذف',
        'payment_cycle' => 'أسبوعي',
        'amount' => 150,
        'has_sisters_discount' => false,
        'status' => 'active',
    ]);

    $deleted = app(DeleteFeePlanAction::class)->handle(['feePlan' => $feePlan]);

    expect($deleted)->toBeTrue();
    $this->assertDatabaseMissing('fee_plans', ['id' => $feePlan->id]);
});

