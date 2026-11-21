<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed the circulation settings.
     */
    public function run(): void
    {
        Setting::put(Setting::KEY_FINE_PER_DAY, '5000');
        Setting::put(Setting::KEY_GRACE_DAYS, '2');
        Setting::put(Setting::KEY_LOAN_DAYS, '7');
    }
}
