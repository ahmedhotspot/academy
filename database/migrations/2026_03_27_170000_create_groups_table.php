<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('study_level_id')->constrained('study_levels')->restrictOnDelete();
            $table->foreignId('study_track_id')->constrained('study_tracks')->restrictOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('schedule_type');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_id');
            $table->index('teacher_id');
            $table->index('study_level_id');
            $table->index('study_track_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};

