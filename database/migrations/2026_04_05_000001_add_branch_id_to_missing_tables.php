<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة branch_id إلى teacher_attendances
        if (!Schema::hasColumn('teacher_attendances', 'branch_id')) {
            Schema::table('teacher_attendances', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')
                    ->nullable()
                    ->after('teacher_id');
                $table->index('branch_id');
            });
        }

        // إضافة branch_id إلى teacher_payrolls
        if (!Schema::hasColumn('teacher_payrolls', 'branch_id')) {
            Schema::table('teacher_payrolls', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')
                    ->nullable()
                    ->after('teacher_id');
                $table->index('branch_id');
            });
        }

        // إضافة branch_id إلى assessments
        if (!Schema::hasColumn('assessments', 'branch_id')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')
                    ->nullable()
                    ->after('teacher_id');
                $table->index('branch_id');
            });
        }

        // إضافة branch_id إلى student_progress_logs
        if (!Schema::hasColumn('student_progress_logs', 'branch_id')) {
            Schema::table('student_progress_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')
                    ->nullable()
                    ->after('teacher_id');
                $table->index('branch_id');
            });
        }

        // إضافة branch_id إلى payments (عبر علاقة الطالب)
        // ملاحظة: هذا قد يتطلب الربط عبر student.branch_id
        // سيتم التعامل معه في الموديل بدلاً من الجدول
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('teacher_attendances', 'branch_id')) {
            Schema::table('teacher_attendances', function (Blueprint $table) {
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('teacher_payrolls', 'branch_id')) {
            Schema::table('teacher_payrolls', function (Blueprint $table) {
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('assessments', 'branch_id')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('student_progress_logs', 'branch_id')) {
            Schema::table('student_progress_logs', function (Blueprint $table) {
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};

