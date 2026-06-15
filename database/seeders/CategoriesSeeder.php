<?php

namespace Database\Seeders;

use App\Models\Categories; // Sesuaikan dengan nama modelmu
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Ilmu Komputer', 'Matematika', 'Fisika', 'Bahasa', 'Seni', 'Teknologi', 'Kesehatan', 'Ekonomi', 'Sosial', 'Agama', 
                        'Bahasa Inggris', 'Sejarah', 'Geografi', 'Psikologi', 'Filsafat', 'Sastra', 'Hukum', 'Bisnis', 'Pendidikan', 
                        'Lingkungan', 'Teknik', 'Kimia', 'Desain', 'Musik', 'Olahraga',];

        foreach ($categories as $cat) {
            Categories::create([
                'name' => $cat,
                'slug' => Str::slug($cat),
            ]);
        }
    }
}