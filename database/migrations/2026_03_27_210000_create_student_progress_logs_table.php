<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('progress_date');
            $table->string('memorization_amount');
            $table->string('revision_amount');
            $table->string('tajweed_evaluation');
            $table->string('tadabbur_evaluation');
            $table->text('repeated_mistakes')->nullable();
            $table->string('mastery_level');
            $table->string('commitment_status');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('group_id');
            $table->index('teacher_id');
            $table->index('progress_date');
            $table->index('commitment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_progress_logs');
    }
};

