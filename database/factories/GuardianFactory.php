<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian>
 */
class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'full_name' => $this->faker->name(),
            'phone' => '05' . $this->faker->numerify('########'),
            'whatsapp' => null,
            'status' => 'active',
        ];
    }
}

