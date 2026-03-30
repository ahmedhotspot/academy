<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'institution_name'    => 'أكاديمية القرآن الكريم',
            'institution_address' => 'المملكة العربية السعودية',
            'institution_phone'   => '',
            'institution_email'   => '',
            'institution_logo'    => '',
            'last_backup_at'      => null,
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}

