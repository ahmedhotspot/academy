<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_code', 50)->nullable()->after('branch_id');
        });

        DB::table('students')
            ->select('id')
            ->whereNull('student_code')
            ->orderBy('id')
            ->get()
            ->each(function (object $student) {
                DB::table('students')
                    ->where('id', $student->id)
                    ->update([
                        'student_code' => sprintf('STD-%06d', $student->id),
                    ]);
            });

        Schema::table('students', function (Blueprint $table) {
            $table->unique('student_code');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['student_code']);
            $table->dropColumn('student_code');
        });
    }
};
