<?php

namespace Database\Factories;

use App\Models\StudyTrack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudyTrack>
 */
class StudyTrackFactory extends Factory
{
    protected $model = StudyTrack::class;

    public function definition(): array
    {
        return [
            'name' => 'مسار ' . $this->faker->unique()->word(),
            'status' => 'active',
        ];
    }
}

