<?php

use App\Models\Branch;
use App\Models\FeePlan;
use App\Models\Notification;
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
        'remaining_due_date' => now()->subDays(3),
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
        'remaining_due_date' => now()->subDays(1),
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
        'remaining_due_date' => now()->subDays(2),
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
        'remaining_due_date' => now()->subDays(1),
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

