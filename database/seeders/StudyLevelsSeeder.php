<?php

namespace Database\Seeders;

use App\Models\StudyLevel;
use Illuminate\Database\Seeder;

class StudyLevelsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['مبتدئ', 'متوسط', 'متقدم', 'إجازة'] as $name) {
            StudyLevel::query()->updateOrCreate(
                ['name' => $name],
                ['status' => 'active']
            );
        }

        $this->command->info('تم إنشاء مستويات الدراسة بنجاح.');
    }
}

