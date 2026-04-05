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
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->foreign('branch_id', 'teacher_attendances_branch_id_foreign')
                ->references('id')
                ->on('branches')
                ->restrictOnDelete();
        });

        Schema::table('teacher_payrolls', function (Blueprint $table) {
            $table->foreign('branch_id', 'teacher_payrolls_branch_id_foreign')
                ->references('id')
                ->on('branches')
                ->restrictOnDelete();
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->foreign('branch_id', 'assessments_branch_id_foreign')
                ->references('id')
                ->on('branches')
                ->restrictOnDelete();
        });

        Schema::table('student_progress_logs', function (Blueprint $table) {
            $table->foreign('branch_id', 'student_progress_logs_branch_id_foreign')
                ->references('id')
                ->on('branches')
                ->restrictOnDelete();
        });

        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->foreign('branch_id', 'student_enrollments_branch_id_foreign')
                ->references('id')
                ->on('branches')
                ->restrictOnDelete();
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->foreign('branch_id', 'student_subscriptions_branch_id_foreign')
                ->references('id')
                ->on('branches')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropForeign('teacher_attendances_branch_id_foreign');
        });

        Schema::table('teacher_payrolls', function (Blueprint $table) {
            $table->dropForeign('teacher_payrolls_branch_id_foreign');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign('assessments_branch_id_foreign');
        });

        Schema::table('student_progress_logs', function (Blueprint $table) {
            $table->dropForeign('student_progress_logs_branch_id_foreign');
        });

        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropForeign('student_enrollments_branch_id_foreign');
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->dropForeign('student_subscriptions_branch_id_foreign');
        });
    }
};

