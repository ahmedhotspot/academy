<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('group_id')->constrained('groups')->restrictOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('student_id');
            $table->index('group_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};

