<?php

namespace Database\Seeders;

use App\Models\StudyTrack;
use Illuminate\Database\Seeder;

class StudyTracksSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['عربي', 'أعجمي'] as $name) {
            StudyTrack::query()->updateOrCreate(
                ['name' => $name],
                ['status' => 'active']
            );
        }

        $this->command->info('تم إنشاء مسارات الدراسة بنجاح.');
    }
}

