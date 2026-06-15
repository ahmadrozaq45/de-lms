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
    }
}
