<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->restrictOnDelete();
            $table->string('day_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('group_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_schedules');
    }
};

