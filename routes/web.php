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
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecommendationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

     Route::get('/web/ai/providers', [\App\Http\Controllers\AiAnalysisController::class, 'availableProviders'])
     ->name('ai.providers');

    // Profile (semua role)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── ADMIN ────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/report', [ReportController::class, 'admin'])->name('report');
    });

    // ── SETTINGS (semua role) ─────────────────────────────────────────────
    Route::get('/settings',          [SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password',[SettingController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/settings/account',[SettingController::class, 'deleteAccount'])->name('settings.delete');

         // Admin-only setting routes
    Route::patch('/settings/api',         [SettingController::class, 'updateApi'])->name('settings.api');
    Route::patch('/settings/theme',       [SettingController::class, 'updateTheme'])->name('settings.theme');
    Route::patch('/settings/landingpage', [SettingController::class, 'updateLandingPage'])->name('settings.landingpage');
    Route::patch('/settings/certificate', [SettingController::class, 'updateCertificate'])->name('settings.certificate');

    // Teacher-only setting route
    Route::patch('/settings/ai-preference', [SettingController::class, 'updateAiPreference'])->name('settings.ai-preference');

    // ── GURU ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'teacher'])->name('dashboard');
        Route::get('/report',    [ReportController::class, 'teacher'])->name('report');

        Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');

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
        Route::get('/report',    [ReportController::class, 'student'])->name('report');
        Route::post('/enroll', [EnrollmentController::class, 'store'])->name('enroll');

        Route::get('/courses',                       [CourseController::class, 'myCourses'])
             ->name('courses.index');
        Route::get('/courses/{id}',                  [CourseController::class, 'show'])
             ->name('courses.show');
        Route::get('/materials/{id}',                [MaterialController::class, 'show'])
             ->name('materials.read');
        Route::get('/assignments/{id}',              [AssignmentController::class, 'show'])
             ->name('assignments.show');
        Route::post('/assignments/{id}/submit',      [AssignmentController::class, 'submit'])
             ->name('assignments.submit');
        Route::get('/materials/{id}/download',          [MaterialController::class, 'download'])
             ->name('materials.download');
        Route::post('/materials/{id}/complete',          [MaterialController::class, 'markComplete'])
             ->name('materials.complete');

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

     // ── AI GENERATE (web route agar pakai session) ──────────────────────────
    Route::post('/web/ai/generate-for-student', [\App\Http\Controllers\AiAnalysisController::class, 'generateForStudent'])
         ->name('ai.generate-for-student');

    // ── RECOMMENDATIONS (semua role) ─────────────────────────────────────
     Route::prefix('recommendations')->name('recommendations.')->group(function () {
        Route::get('/',               [RecommendationController::class, 'index'])    ->name('index');
        Route::get('/widget',         [RecommendationController::class, 'widget'])   ->name('widget');
        Route::post('/{id}/feedback', [RecommendationController::class, 'feedback']) ->name('feedback');
        Route::post('/refresh',       [RecommendationController::class, 'refresh'])  ->name('refresh');
        Route::get('/{id}/goto',      [RecommendationController::class, 'goto'])     ->name('goto');
     });
});

require __DIR__.'/auth.php';