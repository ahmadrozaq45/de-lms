<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\{CourseContentController, AcademicController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return redirect()->route($role . '.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ADMIN
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    });

    // GURU
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [CourseContentController::class, 'index'])->name('dashboard');

        // Kursus
        Route::get('/courses',         [CourseContentController::class, 'manageCourses'])->name('courses.index');
        Route::post('/courses',        [CourseContentController::class, 'storeCourse'])->name('courses.store');
        Route::get('/courses/{id}',    [CourseContentController::class, 'show'])->name('courses.show');

        // Modul (di dalam kursus)
        Route::post('/courses/{courseId}/modules', [CourseContentController::class, 'addModule'])
             ->name('courses.modules.store');

        // Materi (di dalam modul)
        Route::post('/modules/{moduleId}/materials', [CourseContentController::class, 'addMaterial'])
             ->name('modules.materials.store');

        // Review Submissions
        Route::get('/reviews',          [AcademicController::class, 'reviewIndex'])->name('reviews.index');
        Route::patch('/reviews/{id}',   [AcademicController::class, 'reviewUpdate'])->name('reviews.update');
    });

    // SISWA
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', function () {
            $availableCourses = \App\Models\Course::all();
            return view('student.dashboard', compact('availableCourses'));
        })->name('dashboard');
    });

    Route::post('/enroll', [AcademicController::class, 'enroll'])->name('enroll');
});

require __DIR__.'/auth.php';    