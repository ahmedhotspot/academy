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
        // إضافة branch_id إلى student_enrollments
        if (!Schema::hasColumn('student_enrollments', 'branch_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')
                    ->nullable()
                    ->after('id');
                $table->index('branch_id');
            });
        }

        // إضافة branch_id إلى student_subscriptions
        if (!Schema::hasColumn('student_subscriptions', 'branch_id')) {
            Schema::table('student_subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')
                    ->nullable()
                    ->after('id');
                $table->index('branch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('student_enrollments', 'branch_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('student_subscriptions', 'branch_id')) {
            Schema::table('student_subscriptions', function (Blueprint $table) {
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};

