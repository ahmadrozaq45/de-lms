<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name'        => 'Selamat Datang!',
                'description' => 'Badge untuk pengguna yang pertama kali login.',
                'icon'        => '👋',
                'type'        => 'first_login',
            ],
            [
                'name'        => 'Kursus Selesai',
                'description' => 'Menyelesaikan semua materi dalam sebuah kursus.',
                'icon'        => '🎓',
                'type'        => 'course_complete',
            ],
            [
                'name'        => 'Nilai Sempurna!',
                'description' => 'Mendapat skor 100 dalam sebuah quiz.',
                'icon'        => '⭐',
                'type'        => 'quiz_perfect',
            ],
            [
                'name'        => 'Lulus Ujian',
                'description' => 'Berhasil lulus dalam sebuah quiz.',
                'icon'        => '✅',
                'type'        => 'quiz_passed',
            ],
            [
                'name'        => 'Materi Selesai',
                'description' => 'Menyelesaikan semua materi dalam sebuah modul.',
                'icon'        => '📚',
                'type'        => 'material_complete',
            ],
            [
                'name'        => 'Tugas Dikumpulkan',
                'description' => 'Mengumpulkan tugas pertama.',
                'icon'        => '📝',
                'type'        => 'assignment_submitted',
            ],
            [
                'name'        => 'Streak 3 Hari',
                'description' => 'Belajar 3 hari berturut-turut.',
                'icon'        => '🔥',
                'type'        => 'streak_3',
            ],
            [
                'name'        => 'Streak 7 Hari',
                'description' => 'Belajar 7 hari berturut-turut.',
                'icon'        => '🚀',
                'type'        => 'streak_7',
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['type' => $badge['type']], $badge);
        }
    }
}
