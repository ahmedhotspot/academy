<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Guardian;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class GuardiansSeeder extends Seeder
{
    public function run(): void
    {
        $branchIds = Branch::query()->pluck('id')->values()->all();

        if ($branchIds === []) {
            $this->command->warn('لا توجد فروع، تم تخطي إنشاء أولياء الأمور.');

            return;
        }

        $faker = FakerFactory::create('ar_EG');

        $firstNames = ['أحمد', 'محمد', 'علي', 'يوسف', 'عبد الله', 'محمود', 'عمر', 'خالد', 'حسن', 'مصطفى', 'طارق', 'بلال'];
        $lastNames = ['السيد', 'الأنصاري', 'الجمال', 'الهاشمي', 'العمري', 'الحربي', 'المغربي', 'الصاوي', 'العتيبي', 'القحطاني', 'الشمري', 'القرشي'];

        $guardians = [];
        for ($i = 0; $i < 30; $i++) {
            $first = $faker->randomElement($firstNames);
            $middle = $faker->randomElement($firstNames);
            $last = $faker->randomElement($lastNames);
            $phone = $faker->unique()->numerify('011########');

            $guardians[] = [
                'full_name' => "{$first} {$middle} {$last}",
                'phone' => $phone,
                'whatsapp' => random_int(1, 100) <= 80 ? $phone : null,
            ];
        }

        foreach ($guardians as $data) {
            Guardian::query()->updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'branch_id' => $faker->randomElement($branchIds),
                    'full_name' => $data['full_name'],
                    'whatsapp'  => $data['whatsapp'],
                    'status'    => 'active',
                ]
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($guardians) . ' ولي أمر بنجاح.');
    }
}

