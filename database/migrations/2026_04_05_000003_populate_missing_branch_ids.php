<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ملء البيانات الموجودة بـ branch_id الافتراضي
     */
    public function up(): void
    {
        // الحصول على أول فرع صالح أو إنشاء فرع افتراضي عند الحاجة
        $defaultBranchId = DB::table('branches')->orderBy('id')->value('id');

        if (!$defaultBranchId) {
            $defaultBranchId = DB::table('branches')->insertGetId([
                'name' => 'الفرع الرئيسي',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $userBranchIds = DB::table('users')->pluck('branch_id', 'id');
        $studentBranchIds = DB::table('students')->pluck('branch_id', 'id');
        $groupBranchIds = DB::table('groups')->pluck('branch_id', 'id');

        DB::table('teacher_attendances')
            ->select('id', 'teacher_id', 'branch_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($userBranchIds, $defaultBranchId) {
                foreach ($rows as $row) {
                    if ($row->branch_id !== null) {
                        continue;
                    }

                    DB::table('teacher_attendances')
                        ->where('id', $row->id)
                        ->update(['branch_id' => $userBranchIds[$row->teacher_id] ?? $defaultBranchId]);
                }
            });

        DB::table('teacher_payrolls')
            ->select('id', 'teacher_id', 'branch_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($userBranchIds, $defaultBranchId) {
                foreach ($rows as $row) {
                    if ($row->branch_id !== null) {
                        continue;
                    }

                    DB::table('teacher_payrolls')
                        ->where('id', $row->id)
                        ->update(['branch_id' => $userBranchIds[$row->teacher_id] ?? $defaultBranchId]);
                }
            });

        DB::table('assessments')
            ->select('id', 'student_id', 'group_id', 'teacher_id', 'branch_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($studentBranchIds, $groupBranchIds, $userBranchIds, $defaultBranchId) {
                foreach ($rows as $row) {
                    if ($row->branch_id !== null) {
                        continue;
                    }

                    $branchId = $studentBranchIds[$row->student_id]
                        ?? ($row->group_id ? ($groupBranchIds[$row->group_id] ?? null) : null)
                        ?? ($userBranchIds[$row->teacher_id] ?? null)
                        ?? $defaultBranchId;

                    DB::table('assessments')
                        ->where('id', $row->id)
                        ->update(['branch_id' => $branchId]);
                }
            });

        DB::table('student_progress_logs')
            ->select('id', 'student_id', 'group_id', 'teacher_id', 'branch_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($studentBranchIds, $groupBranchIds, $userBranchIds, $defaultBranchId) {
                foreach ($rows as $row) {
                    if ($row->branch_id !== null) {
                        continue;
                    }

                    $branchId = $studentBranchIds[$row->student_id]
                        ?? ($groupBranchIds[$row->group_id] ?? null)
                        ?? ($userBranchIds[$row->teacher_id] ?? null)
                        ?? $defaultBranchId;

                    DB::table('student_progress_logs')
                        ->where('id', $row->id)
                        ->update(['branch_id' => $branchId]);
                }
            });

        DB::table('student_enrollments')
            ->select('id', 'student_id', 'group_id', 'branch_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($studentBranchIds, $groupBranchIds, $defaultBranchId) {
                foreach ($rows as $row) {
                    if ($row->branch_id !== null) {
                        continue;
                    }

                    $branchId = $studentBranchIds[$row->student_id]
                        ?? ($groupBranchIds[$row->group_id] ?? null)
                        ?? $defaultBranchId;

                    DB::table('student_enrollments')
                        ->where('id', $row->id)
                        ->update(['branch_id' => $branchId]);
                }
            });

        DB::table('student_subscriptions')
            ->select('id', 'student_id', 'branch_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($studentBranchIds, $defaultBranchId) {
                foreach ($rows as $row) {
                    if ($row->branch_id !== null) {
                        continue;
                    }

                    DB::table('student_subscriptions')
                        ->where('id', $row->id)
                        ->update(['branch_id' => $studentBranchIds[$row->student_id] ?? $defaultBranchId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نرجع البيانات (يمكن حذفها يدويًا إن لزم)
    }
};

