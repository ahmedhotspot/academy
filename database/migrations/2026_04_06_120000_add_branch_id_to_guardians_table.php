<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            if (! Schema::hasColumn('guardians', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id');
                $table->index('branch_id');
            }
        });

        DB::table('guardians')
            ->whereNull('branch_id')
            ->orderBy('id')
            ->chunkById(200, function ($guardians): void {
                foreach ($guardians as $guardian) {
                    $branchId = DB::table('students')
                        ->where('guardian_id', $guardian->id)
                        ->whereNotNull('branch_id')
                        ->orderBy('id')
                        ->value('branch_id');

                    if ($branchId) {
                        DB::table('guardians')
                            ->where('id', $guardian->id)
                            ->update(['branch_id' => $branchId]);
                    }
                }
            });

        Schema::table('guardians', function (Blueprint $table) {
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropIndex(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};

