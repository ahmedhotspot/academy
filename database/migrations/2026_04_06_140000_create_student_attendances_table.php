<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status')->default('حاضر');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'attendance_date']);
            $table->index('branch_id');
            $table->index('attendance_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};

