<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'title' => 'Pemrograman Web',
            'description' => 'Belajar dasar-dasar pemrograman web menggunakan HTML, CSS, dan JavaScript.',
            'category_id' => 1, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '2',
            'course_code' => 'WEB101',
        ]);

        Course::create([
            'title' => 'Matematika Dasar',
            'description' => 'Pelajari konsep dasar matematika seperti aljabar, geometri, dan trigonometri.',
            'category_id' => 2, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '3',
            'course_code' => 'MATH101',
        ]);

        Course::create([
            'title' => 'Fisika Dasar',
            'description' => 'Pelajari konsep dasar fisika seperti mekanika, termodinamika, dan optik.',
            'category_id' => 3, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '4',
            'course_code' => 'PHYS101',
        ]);

        Course::create([
            'title' => 'Bahasa Inggris Dasar',
            'description' => 'Pelajari dasar-dasar bahasa Inggris untuk komunikasi sehari-hari.',
            'category_id' => 11, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '5',
            'course_code' => 'ENG101',
        ]);

        Course::create([
            'title' => 'Sejarah Dunia',
            'description' => 'Pelajari sejarah dunia dari zaman kuno hingga modern.',
            'category_id' => 12, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '2',
            'course_code' => 'HIST101',
        ]);

        Course::create([
            'title' => 'Geografi Dasar',
            'description' => 'Pelajari konsep dasar geografi seperti peta, iklim, dan sumber daya alam.',
            'category_id' => 13, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '3',
            'course_code' => 'GEO101',
        ]);

        Course::create([
            'title' => 'Psikologi Dasar',
            'description' => 'Pelajari konsep dasar psikologi seperti perilaku manusia, emosi, dan perkembangan.',
            'category_id' => 14, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '4',
            'course_code' => 'PSY101',
        ]);

        Course::create([
            'title' => 'Filsafat Dasar',
            'description' => 'Pelajari konsep dasar filsafat seperti etika, logika, dan metafisika.',
            'category_id' => 15, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '5',
            'course_code' => 'PHIL101',
        ]);

        Course::create([
            'title' => 'Sastra Indonesia',
            'description' => 'Pelajari karya sastra Indonesia dari berbagai genre dan periode.',
            'category_id' => 16, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '2',
            'course_code' => 'LIT101',
        ]);

        Course::create([
            'title' => 'Hukum Dasar',
            'description' => 'Pelajari konsep dasar hukum seperti sistem hukum, hak asasi manusia, dan hukum pidana.',
            'category_id' => 17, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '3',
            'course_code' => 'LAW101',
        ]);

        Course::create([
            'title' => 'Bisnis Dasar',
            'description' => 'Pelajari konsep dasar bisnis seperti manajemen, pemasaran, dan keuangan.',
            'category_id' => 18, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '4',
            'course_code' => 'BUS101',
        ]);

        Course::create([
            'title' => 'Pendidikan Dasar',
            'description' => 'Pelajari konsep dasar pendidikan seperti teori belajar, kurikulum, dan evaluasi.',
            'category_id' => 19, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '5',
            'course_code' => 'EDU101',
        ]);

        Course::create([
            'title' => 'Lingkungan Hidup',
            'description' => 'Pelajari konsep dasar lingkungan hidup seperti ekosistem, perubahan iklim, dan konservasi.',
            'category_id' => 20, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '2',
            'course_code' => 'ENV101',
        ]);

        Course::create([
            'title' => 'Teknik Dasar',
            'description' => 'Pelajari konsep dasar teknik seperti mekanika, listrik, dan material.',
            'category_id' => 21, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '3',
            'course_code' => 'ENGR101',
        ]);

        Course::create([
            'title' => 'Kimia Dasar',
            'description' => 'Pelajari konsep dasar kimia seperti struktur atom, reaksi kimia, dan stoikiometri.',
            'category_id' => 22, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '4',
            'course_code' => 'CHEM101',
        ]);

        Course::create([
            'title' => 'Desain Grafis',
            'description' => 'Pelajari konsep dasar desain grafis seperti tipografi, warna, dan layout.',
            'category_id' => 23, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '5',
            'course_code' => 'DES101',
        ]);

        Course::create([
            'title' => 'Musik Dasar',
            'description' => 'Pelajari konsep dasar musik seperti teori musik, alat musik, dan komposisi.',
            'category_id' => 24, // Sesuaikan dengan ID kategori yang ada
            'teacher_id' => '2',
            'course_code' => 'MUS101',
        ]);
    }
}
