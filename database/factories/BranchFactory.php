<?php

namespace Database\Factories;

use App\Enums\BranchStatusEnum;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => 'فرع ' . $this->faker->city(),
            'status' => BranchStatusEnum::Active,
        ];
    }

    /**
     * حالة الفرع: غير نشط
     */
    public function inactive(): static
    {
        return $this->state(['status' => BranchStatusEnum::Inactive]);
    }
}

