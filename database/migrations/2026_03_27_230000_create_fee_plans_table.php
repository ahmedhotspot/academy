<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('payment_cycle');
            $table->decimal('amount', 10, 2);
            $table->boolean('has_sisters_discount')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('payment_cycle');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_plans');
    }
};

