<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // Profile (semua role)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
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

        // Modul & Materi
        Route::post('/courses/{courseId}/modules',      [ModuleController::class, 'store'])
             ->name('courses.modules.store');
        Route::post('/modules/{moduleId}/materials',    [MaterialController::class, 'store'])
             ->name('modules.materials.store');
        Route::post('/modules/{moduleId}/assignments',  [AssignmentController::class, 'store'])
             ->name('modules.assignments.store');
        Route::get('/materials/{id}/download',          [MaterialController::class, 'download'])
             ->name('materials.download');
        Route::resource('/modules',   ModuleController::class);
        Route::resource('/materials', MaterialController::class);

        // Review & Siswa
        Route::get('/reviews',         [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{id}',  [ReviewController::class, 'update'])->name('reviews.update');
        Route::get('/courses/{courseId}/students', [StudentController::class, 'index'])
             ->name('courses.students');
        Route::post('/courses/{courseId}/students/{enrollmentId}/approve', [StudentController::class, 'approve'])
             ->name('courses.students.approve');
        Route::delete('/courses/{courseId}/students/{enrollmentId}', [StudentController::class, 'destroy'])
             ->name('courses.students.destroy');

        // ── Quiz (Guru) ──────────────────────────────────────────────────────
        // Buat quiz baru untuk sebuah kursus
        Route::get('/courses/{courseId}/quizzes/create', [QuizController::class, 'create'])
             ->name('courses.quizzes.create');
        Route::post('/courses/{courseId}/quizzes',       [QuizController::class, 'storeWeb'])
             ->name('courses.quizzes.store');

        // Detail quiz & tambah soal
        Route::get('/quizzes/{quizId}',             [QuizController::class, 'showTeacher'])
             ->name('quizzes.show');
        Route::post('/quizzes/{quizId}/questions',  [QuizController::class, 'addQuestionWeb'])
             ->name('quizzes.questions.store');
        Route::delete('/quizzes/{quizId}/questions/{questionId}', [QuizController::class, 'deleteQuestion'])
             ->name('quizzes.questions.destroy');

        // Lihat hasil attempt siswa per quiz
        Route::get('/quizzes/{quizId}/results', [QuizController::class, 'results'])
             ->name('quizzes.results');

        // Hapus quiz
        Route::delete('/quizzes/{quizId}', [QuizController::class, 'destroy'])
             ->name('quizzes.destroy');
    });

    // ── SISWA ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');

        Route::post('/enroll', [EnrollmentController::class, 'store'])->name('enroll');

        Route::get('/courses/{id}',                  [CourseController::class, 'show'])
             ->name('courses.show');
        Route::get('/materials/{id}',                [MaterialController::class, 'show'])
             ->name('materials.read');
        Route::get('/assignments/{id}',              [AssignmentController::class, 'show'])
             ->name('assignments.show');
        Route::post('/assignments/{id}/submit',      [AssignmentController::class, 'submit'])
             ->name('assignments.submit');

        // ── Quiz (Siswa) ─────────────────────────────────────────────────────
        // Halaman detail quiz (sebelum mulai)
        Route::get('/quizzes/{quizId}',              [QuizController::class, 'showStudent'])
             ->name('quizzes.show');

        // Mulai attempt → buat record attempt, redirect ke halaman kerjakan
        Route::post('/quizzes/{quizId}/start',       [QuizController::class, 'startWeb'])
             ->name('quizzes.start');

        // Halaman kerjakan soal (pakai attempt yang sudah dibuat)
        Route::get('/attempts/{attemptId}/work',     [QuizController::class, 'workAttempt'])
             ->name('quizzes.work');

        // Submit semua jawaban
        Route::post('/attempts/{attemptId}/submit',  [QuizController::class, 'submitWeb'])
             ->name('quizzes.submit');

        // Halaman hasil quiz
        Route::get('/attempts/{attemptId}/result',   [QuizController::class, 'resultWeb'])
             ->name('quizzes.result');
    });
});

require __DIR__.'/auth.php';