<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\{CourseContentController, AcademicController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect Dashboard Utama berdasarkan Role
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    // Memastikan redirect ke route name yang tepat: admin.dashboard, teacher.dashboard, atau student.dashboard
    return redirect()->route($role . '.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Rute Profile (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wilayah ADMIN
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });

    // Wilayah GURU (Teacher)
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [CourseContentController::class, 'index'])->name('dashboard');
        Route::post('/courses', [CourseContentController::class, 'storeCourse'])->name('courses.store');
    });

    // Wilayah SISWA (Student)
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', function () {
            $availableCourses = \App\Models\Course::all();
            return view('student.dashboard', compact('availableCourses'));
        })->name('dashboard');
    });

    // Enroll (Digunakan di Dashboard Siswa)
    Route::post('/enroll', [AcademicController::class, 'enroll'])->name('enroll');

});

require __DIR__.'/auth.php';