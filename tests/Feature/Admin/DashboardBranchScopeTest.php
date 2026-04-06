<?php

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\User;
use App\Services\Dashboard\DashboardStatsService;
use Spatie\Permission\Models\Role;

it('يعرض إحصائيات الداشبورد للسكرتيرة حسب فرعها فقط', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    $secretary = User::factory()->create(['branch_id' => $branchA->id]);
    $secretary->assignRole('السكرتيرة');

    $teacherInBranch = User::factory()->create(['branch_id' => $branchA->id]);
    $teacherInBranch->assignRole('المعلم');

    $teacherOtherBranch = User::factory()->create(['branch_id' => $branchB->id]);
    $teacherOtherBranch->assignRole('المعلم');

    Guardian::factory()->create(['branch_id' => $branchA->id]);
    Guardian::factory()->create(['branch_id' => $branchB->id]);

    $cards = collect(app(DashboardStatsService::class)->buildDashboardData($secretary)['statsCards'])
        ->keyBy('title');

    expect($cards['إجمالي المعلمين']['value'])->toBe(1)
        ->and($cards['إجمالي أولياء الأمور']['value'])->toBe(1)
        ->and($cards['عدد الفروع']['value'])->toBe(1);
});

it('يعرض إحصائيات كل الفروع للمشرف العام', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    Role::findOrCreate('المعلم', 'web');

    $superAdmin = User::factory()->create(['branch_id' => null]);

    $teacherInBranch = User::factory()->create(['branch_id' => $branchA->id]);
    $teacherInBranch->assignRole('المعلم');

    $teacherOtherBranch = User::factory()->create(['branch_id' => $branchB->id]);
    $teacherOtherBranch->assignRole('المعلم');

    Guardian::factory()->create(['branch_id' => $branchA->id]);
    Guardian::factory()->create(['branch_id' => $branchB->id]);

    $cards = collect(app(DashboardStatsService::class)->buildDashboardData($superAdmin)['statsCards'])
        ->keyBy('title');

    expect($cards['إجمالي المعلمين']['value'])->toBe(2)
        ->and($cards['إجمالي أولياء الأمور']['value'])->toBe(2)
        ->and($cards['عدد الفروع']['value'])->toBe(2);
});

