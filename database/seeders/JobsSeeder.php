<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobPosting;
use Illuminate\Database\Seeder;

class JobsSeeder extends Seeder
{
    /**
     * Demo job postings so the frontend listing has something to show.
     * Without these, GET /job-postings returns an empty page and every
     * listing/dashboard screen looks broken rather than simply empty.
     */
    public function run(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->command?->warn('JobsSeeder skipped: no company found. Run CompaniesSeeder first.');

            return;
        }

        $jobs = [
            [
                'title' => 'Frontend Developer',
                'description' => 'Membangun antarmuka web yang responsif dan cepat bersama tim produk, berkolaborasi erat dengan desainer UI/UX. Menguasai React dan TypeScript menjadi nilai tambah.',
                'location' => 'Bandung',
                'salary' => 4500000,
                'employment_type' => 'Full Time',
            ],
            [
                'title' => 'Backend Engineer',
                'description' => 'Merancang dan memelihara REST API serta skema database untuk platform marketplace yang sedang berkembang pesat.',
                'location' => 'Remote',
                'salary' => 6000000,
                'employment_type' => 'Contract',
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'Merancang wireframe, prototipe, dan design system untuk aplikasi mobile banking bersama tim produk.',
                'location' => 'Jakarta',
                'salary' => 3800000,
                'employment_type' => 'Internship',
            ],
            [
                'title' => 'Data Analyst',
                'description' => 'Mengolah data penjualan menjadi dashboard dan insight yang mudah dipahami tim manajemen.',
                'location' => 'Yogyakarta',
                'salary' => 5200000,
                'employment_type' => 'Full Time',
            ],
            [
                'title' => 'Social Media Specialist',
                'description' => 'Menyusun kalender konten dan mengelola akun media sosial brand fashion lokal agar makin dikenal luas.',
                'location' => 'Surabaya',
                'salary' => 3200000,
                'employment_type' => 'Part Time',
            ],
            [
                'title' => 'Mobile App Developer',
                'description' => 'Mengembangkan aplikasi mobile lintas platform (Flutter) untuk startup logistik yang sedang berkembang pesat.',
                'location' => 'Bandung',
                'salary' => 7000000,
                'employment_type' => 'Full Time',
            ],
        ];

        foreach ($jobs as $index => $job) {
            JobPosting::create([
                ...$job,
                'company_id' => $company->id,
                'closing_date' => now()->addDays(30 + $index),
            ]);
        }
    }
}
