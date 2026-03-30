<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Group;
use App\Models\StudyLevel;
use App\Models\StudyTrack;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'teacher_id' => User::factory(),
            'study_level_id' => StudyLevel::factory(),
            'study_track_id' => StudyTrack::factory(),
            'name' => 'حلقة ' . $this->faker->unique()->word(),
            'type' => 'group',
            'schedule_type' => 'weekly',
            'status' => 'active',
        ];
    }
}

