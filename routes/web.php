<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // Profile (semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── ADMIN ────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    });

    // ── GURU ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('dashboard');

        Route::resource('courses', CourseController::class)
             ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::post('/courses/{courseId}/modules', [ModuleController::class, 'store'])
             ->name('courses.modules.store');
        Route::post('/modules/{moduleId}/materials', [MaterialController::class, 'store'])
             ->name('modules.materials.store');

        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    });

    // ── SISWA ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');

        Route::post('/enroll', [EnrollmentController::class, 'store'])->name('enroll');

        Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');

        Route::get('/materials/{id}', [MaterialController::class, 'show'])->name('materials.read');
    });
});

require __DIR__.'/auth.php';
