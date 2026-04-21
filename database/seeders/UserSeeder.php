<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //ADMIN
        User::create([
            'name' => 'ADMIN DATANG',
            'email' => 'admin@lms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        //TEACHER
        User::create([
            'name' => 'PAPA ZOLA',
            'email' => 'teacher@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher'
        ]);

        //STUDENT
        User::create([
            'name' => 'JARJIT SINGH',
            'email' => 'student@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);
    }
}
