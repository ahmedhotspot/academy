<?php

namespace Database\Seeders;

use App\Models\StudyLevel;
use App\Models\StudyTrack;
use Illuminate\Database\Seeder;

class StudyCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['مبتدئ', 'متوسط', 'متقدم', 'إجازات'] as $levelName) {
            StudyLevel::query()->updateOrCreate(
                ['name' => $levelName],
                ['status' => 'active']
            );
        }

        foreach (['عربي', 'أعجمي'] as $trackName) {
            StudyTrack::query()->updateOrCreate(
                ['name' => $trackName],
                ['status' => 'active']
            );
        }
    }
}

