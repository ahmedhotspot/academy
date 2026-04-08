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
            'student_code' => 'STD-' . $this->faker->unique()->numerify('####'),
            'guardian_id' => null,
            'full_name' => $this->faker->name(),
            'enrollment_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'birth_date' => $this->faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d'),
            'age' => $this->faker->numberBetween(5, 18),
            'nationality' => 'سعودي',
            'identity_number' => (string) $this->faker->numerify('##########'),
            'identity_expiry_date' => $this->faker->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'residency_number' => null,
            'residency_expiry_date' => null,
            'phone' => '05' . $this->faker->numerify('########'),
            'whatsapp' => null,
            'status' => 'active',
        ];
    }
}

