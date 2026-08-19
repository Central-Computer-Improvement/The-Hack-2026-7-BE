<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'company_name' => 'PT SkillBridge Indonesia',
            'description' => 'Perusahaan teknologi yang bergerak di bidang pengembangan software.',
            'location' => 'Bandung',
            'website' => 'https://skillbridge.test',
            'logo' => null,
        ]);
    }
}