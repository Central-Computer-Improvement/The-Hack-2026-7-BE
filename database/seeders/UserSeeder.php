<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        User::create([
            'name' => 'Admin SkillBridge',
            'email' => 'admin@skillbridge.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'education' => 'S1 Rekayasa Perangkat Lunak',
            'bio' => 'Administrator SkillBridge',
            'company_id' => null,
        ]);

        User::create([
            'name' => 'Company SkillBridge',
            'email' => 'company@skillbridge.test',
            'password' => Hash::make('password123'),
            'role' => 'company',
            'phone' => '081234567891',
            'education' => 'S1 Teknik Informatika',
            'bio' => 'HR dari PT SkillBridge Indonesia',
            'company_id' => $company->id,
        ]);

        User::create([
            'name' => 'Applicant SkillBridge',
            'email' => 'applicant@skillbridge.test',
            'password' => Hash::make('password123'),
            'role' => 'applicant',
            'phone' => '081234567892',
            'education' => 'S1 Rekayasa Perangkat Lunak',
            'bio' => 'Software engineering enthusiast',
            'company_id' => null,
        ]);
    }
}