<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Guardian;
use App\Models\Student;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('ar_EG');

        $branches   = Branch::query()->pluck('id')->toArray();
        $guardians  = Guardian::query()->pluck('id')->toArray();

        // أسماء ذكور
        $maleNames = [
            'أحمد عبد الله المصري', 'محمد يوسف الأنصاري', 'عبد الرحمن علي سليم',
            'عمر خالد الزهراني',    'يوسف سعيد الحربي',   'إبراهيم ناصر القحطاني',
            'عبد الله محمود النور', 'زياد حسن الشمري',    'بلال عمر السبيعي',
            'عبد العزيز راشد الغامدي','مصطفى فاروق طاهر', 'حمزة جمال الكريم',
            'خالد رامي الشهراني',   'طارق أنس البلوشي',   'صالح عادل المغربي',
            'منذر وائل الحميد',     'أيمن سامر الحجازي',  'رياض حاتم العسيري',
            'سليمان أكرم الجبري',   'علي أحمد البكري',    'حسام الدين كريم العلي',
            'ماجد سلطان الدوسري',   'فيصل جابر المطيري',  'نواف رفيق القريشي',
            'وليد منصور الثبيتي',   'عبد الكريم هاني حجاج','سامر لؤي بسيوني',
            'أنس جلال العمري',      'زاهر مروان حسب الله','حيدر قاسم الصالح',
            'إياد نبيل الفقيه',     'كريم طارق الدجاني',  'شادي عمر الجندي',
            'عثمان صالح المحيسن',   'أسامة عبد اللطيف',   'محمود سمير الصاوي',
            'عمرو حمدي التهامي',    'بشير نعمان الرفاعي', 'أمجد طلال العجمي',
            'باسل مجدي شحاتة',      'عبد المجيد سليمان',  'رشيد عثمان الوليد',
            'كمال لطفي منير',       'فارس نادر حميدة',    'هيثم قاسم الغزاوي',
            'ثابت حمزة الهاشمي',    'معاذ عمران البربري', 'رافع حسين الأيوبي',
            'تميم سعد المعيوف',     'إسحاق رمضان الرقيبي',
        ];

        // أسماء إناث
        $femaleNames = [
            'فاطمة أحمد الزهراء',   'مريم خالد السلطان',  'عائشة محمود النجار',
            'خديجة عمر البدوي',     'سارة يوسف المنصور',  'نورا إبراهيم الشريف',
            'هدى سامي الفقيه',      'آمنة رشاد الحسيني',  'رقية طارق العتيبي',
            'أسماء ناصر الشهري',    'زينب بلال القرشي',   'ميسون هاني الصمادي',
            'لينا وائل الجاسم',     'ريم عبد الله البكر', 'دانة سلطان المطيري',
            'شهد أنور المحيسن',     'لمياء راشد الغامدي', 'منال كريم الزبيدي',
            'إيمان حمدي السيوطي',   'رانيا مصطفى عوده',   'هيا جلال الدريهم',
            'روان صالح الشمري',     'وفاء جمال الحيدري',  'أميرة سعيد البلوشي',
            'بتول محمد حجاج',       'سلمى عادل السيد',    'حنان ماجد الخالدي',
            'نهال طلال صبري',       'إسراء وليد منير',    'هالة إبراهيم ثابت',
            'عبير أكرم الجندي',     'رهف يحيى الزيد',     'ابتسام نور الدين',
            'مسرة حسين الصالح',     'غادة صبحي الوليد',   'تقى لطفي النجار',
            'ملك رامي الحوت',       'دينا أيمن البسيوني', 'سجود عمران حمدان',
            'أريج نبيل الصاوي',     'تهاني هشام الكردي',  'إلهام سمير مراد',
            'وسام عبد المجيد',      'مها قاسم العسيري',   'نداء فيصل الأحمدي',
            'سناء رياض خيري',       'حياة منذر العمدة',   'فرح أنس البوسعيدي',
            'نسرين كمال الدردير',   'عبلة فاروق الجعفري',
        ];

        $nationalities = [
            'مصري', 'مصري', 'مصري', 'مصري', 'مصري', 'مصري',
            'سعودي', 'سعودي', 'سعودي',
            'سوداني', 'يمني', 'أردني', 'لبناني', 'سوري', 'إندونيسي',
        ];

        $statuses      = ['active', 'active', 'active', 'active', 'active', 'inactive', 'inactive'];
        $allStudents   = [];
        $phoneCounter  = 1;
        $guardianIds   = array_values($guardians);
        $guardianCount = count($guardianIds);

        // -- ذكور --
        foreach ($maleNames as $i => $name) {
            $age        = ($i % 3 === 0)
                ? $faker->numberBetween(5, 10)
                : (($i % 3 === 1) ? $faker->numberBetween(11, 15) : $faker->numberBetween(16, 20));
            $phone      = '011' . str_pad($phoneCounter, 8, '0', STR_PAD_LEFT);
            $allStudents[] = [
                'full_name'       => $name,
                'age'             => $age,
                'phone'           => $phone,
                'whatsapp'        => ($phoneCounter % 4 !== 0) ? $phone : null,
                'nationality'     => $faker->randomElement($nationalities),
                'identity_number' => null,
                'branch_id'       => $branches[$phoneCounter % count($branches)],
                'guardian_id'     => $guardianIds[($phoneCounter - 1) % $guardianCount],
                'status'          => $statuses[$phoneCounter % count($statuses)],
            ];
            $phoneCounter++;
        }

        // -- إناث --
        foreach ($femaleNames as $i => $name) {
            $age        = ($i % 3 === 0)
                ? $faker->numberBetween(5, 10)
                : (($i % 3 === 1) ? $faker->numberBetween(11, 15) : $faker->numberBetween(16, 20));
            $phone      = '011' . str_pad($phoneCounter, 8, '0', STR_PAD_LEFT);
            $allStudents[] = [
                'full_name'       => $name,
                'age'             => $age,
                'phone'           => $phone,
                'whatsapp'        => ($phoneCounter % 3 !== 0) ? $phone : null,
                'nationality'     => $faker->randomElement($nationalities),
                'identity_number' => null,
                'branch_id'       => $branches[$phoneCounter % count($branches)],
                'guardian_id'     => $guardianIds[($phoneCounter - 1) % $guardianCount],
                'status'          => $statuses[$phoneCounter % count($statuses)],
            ];
            $phoneCounter++;
        }

        foreach ($allStudents as $data) {
            Student::query()->updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'full_name'       => $data['full_name'],
                    'age'             => $data['age'],
                    'whatsapp'        => $data['whatsapp'],
                    'nationality'     => $data['nationality'],
                    'identity_number' => $data['identity_number'],
                    'branch_id'       => $data['branch_id'],
                    'guardian_id'     => $data['guardian_id'],
                    'status'          => $data['status'],
                ]
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($allStudents) . ' طالب/طالبة بنجاح.');
    }
}

