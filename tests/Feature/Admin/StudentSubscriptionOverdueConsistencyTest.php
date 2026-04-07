<?php

use App\Models\Branch;
use App\Models\FeePlan;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\User;
use App\Services\Admin\NotificationAutoCheckService;
use App\Services\Admin\StudentSubscriptionManagementService;
use App\Services\Dashboard\DashboardStatsService;
use Illuminate\Support\Facades\Cache;

it('يحسب ملخص الاشتراكات الطلاب المتأخرين بناء على الاستحقاق المتجاوز وليس الحالة فقط', function () {
    $branch = Branch::factory()->create();
    $student = Student::factory()->create(['branch_id' => $branch->id]);

    $feePlan = FeePlan::query()->create([
        'name' => 'خطة شهرية',
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
        'paid_amount' => 100,
        'remaining_amount' => 200,
        'status' => 'نشط', // intentionally not set to "متأخر"
        'start_date' => now()->subMonth(),
        'due_date' => now()->subDays(10),
        'remaining_due_date' => now()->addDays(10),
    ]);

    StudentSubscription::query()->create([
        'branch_id' => $branch->id,
        'student_id' => $student->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 150,
        'discount_amount' => 0,
        'final_amount' => 150,
        'paid_amount' => 50,
        'remaining_amount' => 100,
        'status' => 'متأخر',
        'start_date' => now()->subDays(20),
        'due_date' => now()->subDays(5),
        'remaining_due_date' => now()->addDays(5),
    ]);

    $summary = app(StudentSubscriptionManagementService::class)->reportSummary();

    expect($summary['overdue'])->toBe(2)
        ->and($summary['overdueStudents'])->toBe(1);
});

it('يعرض الداشبورد عدد الطلاب المتأخرين الصحيح حتى لو حالة الاشتراك نشط', function () {
    $branch = Branch::factory()->create();
    $student = Student::factory()->create(['branch_id' => $branch->id]);

    $feePlan = FeePlan::query()->create([
        'name' => 'خطة أسبوعية',
        'payment_cycle' => 'أسبوعي',
        'amount' => 100,
        'has_sisters_discount' => false,
        'status' => 'active',
    ]);

    StudentSubscription::query()->create([
        'branch_id' => $branch->id,
        'student_id' => $student->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 100,
        'discount_amount' => 0,
        'final_amount' => 100,
        'paid_amount' => 0,
        'remaining_amount' => 100,
        'status' => 'نشط',
        'start_date' => now()->subDays(12),
        'due_date' => now()->subDays(4),
        'remaining_due_date' => now()->addDays(7),
    ]);

    $cards = collect(app(DashboardStatsService::class)->buildDashboardData(null)['statsCards'])->keyBy('title');

    expect($cards['الطلاب المتأخرون']['value'])->toBe(1);
});

it('ينشئ إشعارا للمتأخرات المالية عند وجود اشتراك متجاوز لتاريخ الاستحقاق', function () {
    Cache::flush();

    $branch = Branch::factory()->create();
    $student = Student::factory()->create(['branch_id' => $branch->id]);
    $user = User::factory()->create(['branch_id' => $branch->id]);

    $feePlan = FeePlan::query()->create([
        'name' => 'خطة نصف شهرية',
        'payment_cycle' => 'نصف شهري',
        'amount' => 250,
        'has_sisters_discount' => false,
        'status' => 'active',
    ]);

    $subscription = StudentSubscription::query()->create([
        'branch_id' => $branch->id,
        'student_id' => $student->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 250,
        'discount_amount' => 0,
        'final_amount' => 250,
        'paid_amount' => 100,
        'remaining_amount' => 150,
        'status' => 'نشط',
        'start_date' => now()->subDays(20),
        'due_date' => now()->subDays(5),
        'remaining_due_date' => now()->addDays(3),
    ]);

    app(NotificationAutoCheckService::class)->checkAndCreateDueReminders();

    $notification = Notification::query()
        ->where('user_id', $user->id)
        ->where('type', 'financial')
        ->whereJsonContains('data->subscription_id', $subscription->id)
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->title)->toContain('متأخرات مالية');
});

it('يعرض مخطط التحصيل والمصروفات في الداشبورد بإجماليات النظام وليس الفرع الحالي فقط', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    $userInBranchA = User::factory()->create(['branch_id' => $branchA->id]);
    $this->actingAs($userInBranchA);

    $studentA = Student::factory()->create(['branch_id' => $branchA->id]);
    $studentB = Student::factory()->create(['branch_id' => $branchB->id]);

    $feePlan = FeePlan::query()->create([
        'name' => 'خطة عامة',
        'payment_cycle' => 'شهري',
        'amount' => 400,
        'has_sisters_discount' => false,
        'status' => 'active',
    ]);

    $subA = StudentSubscription::query()->create([
        'branch_id' => $branchA->id,
        'student_id' => $studentA->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 400,
        'discount_amount' => 0,
        'final_amount' => 400,
        'paid_amount' => 0,
        'remaining_amount' => 400,
        'status' => 'نشط',
        'start_date' => now()->subDays(5),
        'due_date' => now()->addDays(25),
    ]);

    $subB = StudentSubscription::query()->create([
        'branch_id' => $branchB->id,
        'student_id' => $studentB->id,
        'fee_plan_id' => $feePlan->id,
        'amount' => 400,
        'discount_amount' => 0,
        'final_amount' => 400,
        'paid_amount' => 0,
        'remaining_amount' => 400,
        'status' => 'نشط',
        'start_date' => now()->subDays(5),
        'due_date' => now()->addDays(25),
    ]);

    Payment::query()->create([
        'student_id' => $studentA->id,
        'student_subscription_id' => $subA->id,
        'payment_date' => now(),
        'amount' => 100,
        'receipt_number' => 'R-A-1',
    ]);

    Payment::query()->withoutGlobalScope('branch')->create([
        'student_id' => $studentB->id,
        'student_subscription_id' => $subB->id,
        'payment_date' => now(),
        'amount' => 250,
        'receipt_number' => 'R-B-1',
    ]);

    $data = app(DashboardStatsService::class)->buildDashboardData($userInBranchA);

    expect(array_sum($data['charts']['financialByMonth']['collections']))->toBeGreaterThanOrEqual(350.0)
        ->and($data['financialSummary']['system_collection'])->toBeGreaterThanOrEqual(350.0);
});

