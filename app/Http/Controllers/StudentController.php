<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course; // Pastikan model ini sesuai dengan yang dibuat temanmu
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Menampilkan halaman dasbor utama siswa.
     */
    public function dashboard()
    {
        // 1. Ambil data kelas (Courses)
        // Menggunakan latest() untuk mengurutkan dari yang terbaru ditambahkan
        $courses = Course::latest()->get();

        // 2. Siapkan data analitik awal
        // Nanti kamu bisa menambahkan logika lain di sini, misalnya menghitung 
        // tugas yang belum dikerjakan atau persentase penyelesaian materi.
        $totalCourses = $courses->count();
        $user = Auth::user();

        // 3. Kirim data ke file Blade (resources/views/student/dashboard.blade.php)
        return view('student.dashboard', compact('courses', 'totalCourses', 'user'));
    }

    /**
     * Menampilkan detail dari sebuah kelas (course).
     * Termasuk daftar modul dan materi di dalamnya.
     * * @param int $id
     */
    public function showCourse($id)
    {
       $course = Course::with('modules.materials')->findOrFail($id);
    
        // Cek apakah siswa yang login sudah enroll di kelas ini
        $isEnrolled = \App\Models\CourseEnrollment::where('user_id', Auth::id())
                                                ->where('course_id', $id)
                                                ->exists();

        // Kirimkan variabel $isEnrolled ke file Blade
        return view('student.course_detail', compact('course', 'isEnrolled'));
    }
}