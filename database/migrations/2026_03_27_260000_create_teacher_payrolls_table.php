<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('base_salary', 10, 2);
            $table->decimal('deduction_amount', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->string('status')->default('غير مصروف');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('teacher_id');
            $table->index(['month', 'year']);
            $table->index('status');
            $table->unique(['teacher_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_payrolls');
    }
};

