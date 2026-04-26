<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,     // 1. Buat User (Admin, Guru, Siswa) duluan
            CourseSeeder::class,   // 2. Buat Kelas (Induk materi)
            //ModuleSeeder::class,   // 3. Buat Modul (Anak dari kelas)
            //MaterialSeeder::class, // 4. Buat Materi (Anak dari modul)
         ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
