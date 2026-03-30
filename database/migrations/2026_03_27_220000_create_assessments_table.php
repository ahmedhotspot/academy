<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->string('type');
            $table->decimal('memorization_result', 5, 2)->nullable();
            $table->decimal('tajweed_result', 5, 2)->nullable();
            $table->decimal('tadabbur_result', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('group_id');
            $table->index('teacher_id');
            $table->index('assessment_date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};

