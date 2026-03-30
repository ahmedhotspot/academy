<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'guardian_id' => null,
            'full_name' => $this->faker->name(),
            'age' => $this->faker->numberBetween(5, 18),
            'nationality' => 'سعودي',
            'identity_number' => (string) $this->faker->numerify('##########'),
            'phone' => '05' . $this->faker->numerify('########'),
            'whatsapp' => null,
            'status' => 'active',
        ];
    }
}

