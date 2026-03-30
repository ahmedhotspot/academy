<?php

namespace Database\Factories;

use App\Models\StudyLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudyLevel>
 */
class StudyLevelFactory extends Factory
{
    protected $model = StudyLevel::class;

    public function definition(): array
    {
        return [
            'name' => 'مستوى ' . $this->faker->unique()->word(),
            'status' => 'active',
        ];
    }
}

