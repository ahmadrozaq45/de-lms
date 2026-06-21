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
            'name' => 'Default Admin',
            'email' => 'admin@lms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        //TEACHER
        #1
        User::create([
            'name' => 'Mail Ferguson',
            'email' => 'teacher@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #2
        User::create([
            'name' => 'Adi Putra',
            'email' => 'teacher2@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #3
        User::create([
            'name' => 'Michael Winata',
            'email' => 'winata@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #4
        User::create([
            'name' => 'Daniel Budianto',
            'email' => 'daniel@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #5
        User::create([
            'name' => 'Irfan Maulana',
            'email' => 'irfan@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #6
        User::create([
            'name' => 'Agus Santoso',
            'email' => 'agus@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #7
        User::create([
            'name' => 'Sri Agustin',
            'email' => 'sriagustin@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #8
        User::create([
            'name' => 'Evelyn Sanjaya',
            'email' => 'evelyn@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #9
        User::create([
            'name' => 'Tatang Suratang',
            'email' => 'tatang@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        #10
        User::create([
            'name' => 'Andreas Wijaya',
            'email' => 'andreas@lms.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        //STUDENT
        #1
        User::create([
            'name' => 'Jojo Satria',
            'email' => 'student@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #2
        User::create([
            'name' => 'Rizky Pratama',
            'email' => 'rizky@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #3
        User::create([
            'name' => 'Dimas Saputra',
            'email' => 'dimas@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #4
        User::create([
            'name' => 'Fajar Nugroho',
            'email' => 'fajar@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #5
        User::create([
            'name' => 'Rina Wulandari',
            'email' => 'rina@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #6
        User::create([
            'name' => 'Rio Andika',
            'email' => 'rio@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #7
        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'nurhaliza@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #8
        User::create([
            'name' => 'Zahra Fitriani',
            'email' => 'zahra@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #9
        User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'fauzi@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);

        #10
        User::create([
            'name' => 'Lina Marlina',
            'email' => 'lina@lms.com',
            'password' => Hash::make('password'),
            'role' => 'student'
        ]);
    }
}
