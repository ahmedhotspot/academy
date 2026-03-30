<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsToDrop = [
            'code',
            'phone',
            'whatsapp',
            'email',
            'city',
            'address',
            'manager_name',
            'notes',
        ];

        $existingColumns = array_values(array_filter(
            $columnsToDrop,
            fn (string $column): bool => Schema::hasColumn('branches', $column)
        ));

        if ($existingColumns !== []) {
            Schema::table('branches', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }

        if (! Schema::hasColumn('branches', 'status')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('status')->default('active')->after('name');
            });
        }

        if (! Schema::hasColumn('branches', 'deleted_at')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // هذه الهجرة مخصصة لتبسيط الجدول حسب متطلبات المشروع الحالية.
    }
};

