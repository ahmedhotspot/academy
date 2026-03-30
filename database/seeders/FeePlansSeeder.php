<?php

namespace Database\Seeders;

use App\Models\FeePlan;
use Illuminate\Database\Seeder;

class FeePlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                => 'اشتراك شهري — عادي',
                'payment_cycle'       => 'شهري',
                'amount'              => 200.00,
                'has_sisters_discount' => true,
                'status'              => 'active',
            ],
            [
                'name'                => 'اشتراك شهري — مكثف',
                'payment_cycle'       => 'شهري',
                'amount'              => 350.00,
                'has_sisters_discount' => true,
                'status'              => 'active',
            ],
            [
                'name'                => 'اشتراك نصف شهري',
                'payment_cycle'       => 'نصف شهري',
                'amount'              => 120.00,
                'has_sisters_discount' => true,
                'status'              => 'active',
            ],
            [
                'name'                => 'اشتراك أسبوعي',
                'payment_cycle'       => 'أسبوعي',
                'amount'              => 60.00,
                'has_sisters_discount' => false,
                'status'              => 'active',
            ],
            [
                'name'                => 'رسوم بالحلقة',
                'payment_cycle'       => 'بالحلقة',
                'amount'              => 25.00,
                'has_sisters_discount' => false,
                'status'              => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            FeePlan::query()->updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($plans) . ' خطة رسوم بنجاح.');
    }
}

