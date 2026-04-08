<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->date('enrollment_date')->nullable()->after('full_name');
            $table->date('birth_date')->nullable()->after('enrollment_date');
            $table->date('identity_expiry_date')->nullable()->after('identity_number');
            $table->string('gender', 20)->nullable()->after('identity_expiry_date');
            $table->string('residency_number')->nullable()->after('gender');
            $table->date('residency_expiry_date')->nullable()->after('residency_number');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'enrollment_date',
                'birth_date',
                'identity_expiry_date',
                'gender',
                'residency_number',
                'residency_expiry_date',
            ]);
        });
    }
};

