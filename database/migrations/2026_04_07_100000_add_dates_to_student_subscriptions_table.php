<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('status');
            $table->date('due_date')->nullable()->after('start_date');
            $table->date('remaining_due_date')->nullable()->after('due_date');

            $table->index('due_date');
            $table->index('remaining_due_date');
        });
    }

    public function down(): void
    {
        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['due_date']);
            $table->dropIndex(['remaining_due_date']);
            $table->dropColumn(['start_date', 'due_date', 'remaining_due_date']);
        });
    }
};

