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
                $table->foreignId('branch_id')
                    ->after('id')
                    ->constrained('branches')
                    ->cascadeOnDelete();
                $table->index('branch_id');
            });
        }

        // إضافة branch_id إلى student_subscriptions
        if (!Schema::hasColumn('student_subscriptions', 'branch_id')) {
            Schema::table('student_subscriptions', function (Blueprint $table) {
                $table->foreignId('branch_id')
                    ->after('id')
                    ->constrained('branches')
                    ->cascadeOnDelete();
                $table->index('branch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropForeignIdFor('branches', 'branch_id');
            $table->dropIndex(['branch_id']);
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->dropForeignIdFor('branches', 'branch_id');
            $table->dropIndex(['branch_id']);
        });
    }
};

