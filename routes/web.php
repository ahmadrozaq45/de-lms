<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\{CourseContentController, AcademicController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect /dashboard ke dashboard sesuai role masing-masing user
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    // Jaga-jaga jika role tidak dikenali, arahkan ke halaman utama
    $validRoles = ['admin', 'teacher', 'student'];
    if (!in_array($role, $validRoles)) {
        abort(403, 'Role tidak dikenali.');
    }

    return redirect()->route($role . '.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // Profile (semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ADMIN
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    });

    // GURU
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {

        Route::get('/dashboard', [CourseContentController::class, 'dashboard'])->name('dashboard');

        Route::resource('courses', CourseContentController::class)
             ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Modul & Materi
        Route::post('/courses/{courseId}/modules', [CourseContentController::class, 'addModule'])
             ->name('courses.modules.store');
        Route::post('/modules/{moduleId}/materials', [CourseContentController::class, 'addMaterial'])
             ->name('modules.materials.store');

        // Review submission siswa
        Route::get('/reviews', [AcademicController::class, 'reviewIndex'])->name('reviews.index');
        Route::patch('/reviews/{id}', [AcademicController::class, 'reviewUpdate'])->name('reviews.update');
    });

    // SISWA
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', function () {
            $availableCourses = \App\Models\Course::all();
            return view('student.dashboard', compact('availableCourses'));
        })->name('dashboard');

        // Enroll masuk ke sini karena hanya boleh dilakukan siswa
        Route::post('/enroll', [AcademicController::class, 'enroll'])->name('enroll');
    });
});

require __DIR__.'/auth.php';