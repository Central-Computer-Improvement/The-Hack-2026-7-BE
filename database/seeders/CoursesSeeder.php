<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Course catalogue backing the frontend's /kursus page.
     *
     * `video_url` uses ordinary YouTube share links — the frontend turns those
     * into an embed itself, so adding a course later only means pasting a
     * normal YouTube URL here (a direct .mp4 link works too).
     *
     * Categories match the frontend's category list exactly so the category
     * bar filters correctly.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Belajar JavaScript dari Nol',
                'description' => 'Dasar-dasar JavaScript modern, dari variabel sampai async/await, dengan latihan praktik langsung.',
                'category' => 'Teknologi & Informasi',
                'format' => 'Video',
                'duration' => '30 - 60 Menit',
                'rating' => 4.8,
                'video_url' => 'https://www.youtube.com/watch?v=W6NZfCO5SIk',
            ],
            [
                'title' => 'Dasar-Dasar Python untuk Data',
                'description' => 'Pengantar Python untuk pemrosesan data — variabel, list, dan library dasar seperti pandas.',
                'category' => 'Analisa Data',
                'format' => 'Video',
                'duration' => '15 - 30 Menit',
                'rating' => 4.7,
                'video_url' => 'https://www.youtube.com/watch?v=kqtD5dpn9C8',
            ],
            [
                'title' => 'Desain UI/UX dengan Figma',
                'description' => 'Merancang wireframe hingga prototipe interaktif menggunakan Figma, cocok untuk pemula.',
                'category' => 'Grafik & Desain',
                'format' => 'Video',
                'duration' => '30 - 60 Menit',
                'rating' => 4.9,
                'video_url' => 'https://www.youtube.com/watch?v=FTFaQWZBqQ8',
            ],
            [
                'title' => 'Strategi SEO untuk Pemula',
                'description' => 'Cara riset kata kunci, optimasi on-page, dan membaca laporan performa website.',
                'category' => 'Digital Marketing',
                'format' => 'Video',
                'duration' => '10 - 15 Menit',
                'rating' => 4.6,
                'video_url' => 'https://www.youtube.com/watch?v=xsVTqzratPs',
            ],
            [
                'title' => 'Editing Video dengan Premiere Pro',
                'description' => 'Teknik potong, transisi, dan color grading dasar untuk konten YouTube dan media sosial.',
                'category' => 'Vidio & Animasi',
                'format' => 'Video',
                'duration' => '30 - 60 Menit',
                'rating' => 4.8,
                'video_url' => 'https://www.youtube.com/watch?v=1DDZBv6Zdlg',
            ],
            [
                'title' => 'Menulis Artikel SEO yang Menjual',
                'description' => 'Struktur artikel yang ramah mesin pencari sekaligus enak dibaca dan persuasif.',
                'category' => 'Karya Tulis',
                'format' => 'Video',
                'duration' => '10 - 15 Menit',
                'rating' => 4.5,
                'video_url' => 'https://www.youtube.com/watch?v=T7Ha4uUdI08',
            ],
            [
                'title' => 'Dasar Mixing Audio untuk Podcast',
                'description' => 'Level audio, noise reduction, dan mastering ringan untuk podcast yang enak didengar.',
                'category' => 'Musik & Audio',
                'format' => 'Video',
                'duration' => '15 - 30 Menit',
                'rating' => 4.7,
                'video_url' => 'https://www.youtube.com/watch?v=TEbY0Cnh_hI',
            ],
            [
                'title' => 'Membangun Rencana Bisnis dari Nol',
                'description' => 'Menyusun model bisnis, proyeksi keuangan sederhana, dan pitch deck untuk investor.',
                'category' => 'Bisnis',
                'format' => 'Video',
                'duration' => '30 - 60 Menit',
                'rating' => 4.6,
                'video_url' => 'https://www.youtube.com/watch?v=Fqch5OrUPvA',
            ],
            [
                'title' => 'SketchUp untuk Desain Interior',
                'description' => 'Belajar membuat model 3D ruangan dengan SketchUp, dari nol sampai render sederhana.',
                'category' => 'Desain Interior',
                'format' => 'Video',
                'duration' => '10 - 15 Menit',
                'rating' => 4.8,
                'video_url' => 'https://www.youtube.com/watch?v=IjM_ykZ-Zc4',
            ],
            [
                'title' => 'Mengelola Komunitas Online',
                'description' => 'Strategi menjaga engagement, moderasi, dan event untuk komunitas online yang sehat.',
                'category' => 'Sosial',
                'format' => 'Video',
                'duration' => '10 - 15 Menit',
                'rating' => 4.5,
                'video_url' => 'https://www.youtube.com/watch?v=BQOwomPCVsc',
            ],
            [
                'title' => 'Prompt Engineering untuk Pemula',
                'description' => 'Menyusun prompt yang efektif untuk chatbot berbasis LLM dan mengevaluasi hasilnya.',
                'category' => 'Layanan AI',
                'format' => 'Quiz',
                'duration' => '15 - 30 Menit',
                'rating' => 4.7,
            ],
            [
                'title' => 'Analisis Data dengan Excel',
                'description' => 'Pivot table, formula lanjutan, dan visualisasi data untuk laporan bisnis sehari-hari.',
                'category' => 'Analisa Data',
                'format' => 'Quiz',
                'duration' => '30 - 60 Menit',
                'rating' => 4.6,
            ],
        ];

        foreach ($courses as $course) {
            Course::create([
                ...$course,
                'skill_id' => $this->skillIdFor($course['category']),
                'thumbnail_url' => $this->thumbnailFor($course),
            ]);
        }
    }

    /**
     * `courses.skill_id` is a required foreign key, so every course needs a
     * skill row. The catalogue is organised by category rather than skill, so
     * each category doubles as its skill — created on demand, which also means
     * this seeder doesn't depend on SkillsSeeder having run first.
     */
    protected function skillIdFor(string $category): int
    {
        return Skill::firstOrCreate(['name' => $category])->id;
    }

    /**
     * YouTube publishes a predictable thumbnail URL per video id, so video
     * courses get a real matching still for free. Quiz courses fall back to a
     * generic placeholder image.
     */
    protected function thumbnailFor(array $course): string
    {
        $videoUrl = $course['video_url'] ?? null;

        if ($videoUrl && preg_match('/[?&]v=([A-Za-z0-9_-]+)/', $videoUrl, $matches)) {
            return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
        }

        return 'https://picsum.photos/seed/'.urlencode($course['title']).'/600/450';
    }
}
