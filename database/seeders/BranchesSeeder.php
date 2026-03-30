<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['name' => 'فرع القاهرة',     'status' => 'active'],
            ['name' => 'فرع الجيزة',      'status' => 'active'],
            ['name' => 'فرع الإسكندرية', 'status' => 'active'],
        ];

        foreach ($branches as $branch) {
            Branch::query()->updateOrCreate(
                ['name' => $branch['name']],
                ['status' => $branch['status']]
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($branches) . ' فروع بنجاح.');
    }
}

