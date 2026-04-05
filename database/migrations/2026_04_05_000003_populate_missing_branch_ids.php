<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ملء البيانات الموجودة بـ branch_id الافتراضي
     */
    public function up(): void
    {
        // الحصول على أول فرع (الفرع الرئيسي)
        $defaultBranchId = DB::table('branches')->first()?->id;

        if (!$defaultBranchId) {
            // إنشاء فرع افتراضي إذا لم يكن موجودًا
            $defaultBranchId = DB::table('branches')->insertGetId([
                'name' => 'الفرع الرئيسي',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ملء البيانات الموجودة بـ branch_id
        DB::table('teacher_attendances')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        DB::table('teacher_payrolls')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        DB::table('assessments')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        DB::table('student_progress_logs')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        DB::table('student_enrollments')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        DB::table('student_subscriptions')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نرجع البيانات (يمكن حذفها يدويًا إن لزم)
    }
};

