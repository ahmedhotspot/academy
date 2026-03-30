<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupSchedule>
 */
class GroupScheduleFactory extends Factory
{
    protected $model = GroupSchedule::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'day_name' => 'الأحد',
            'start_time' => '16:00:00',
            'end_time' => '17:00:00',
            'status' => 'active',
        ];
    }
}

