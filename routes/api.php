<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua Controller yang sudah diringkas
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AiAnalysisController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan route API untuk aplikasi Anda.
| Route ini otomatis memiliki prefix "/api" dan menggunakan middleware "auth:sanctum".
|
*/

Route::middleware('auth:sanctum')->group(function () {

    // --- 1. USER & PROFILE ---
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'show']);       // Ambil data profil
        Route::put('/', [UserController::class, 'update']);     // Update profil (API)
    });

    // --- 2. COURSE & CONTENT (Course, Module, Material) ---
    Route::prefix('courses')->group(function () {
        Route::post('/', [CourseContentController::class, 'storeCourse']);          // Buat Kursus baru
        Route::post('/{courseId}/modules', [CourseContentController::class, 'addModule']); // Tambah Modul
    });
    Route::post('/modules/{moduleId}/materials', [CourseContentController::class, 'addMaterial']); // Tambah Materi

    // --- 3. ACADEMIC & GRADES (Enrollment, Assignment, Submission, Grade) ---
    Route::post('/enroll', [AcademicController::class, 'enroll']);                  // Daftar ke Kursus
    Route::post('/assignments', [AcademicController::class, 'storeAssignment']);    // Buat Tugas baru
    Route::post('/assignments/{id}/submit', [AcademicController::class, 'submitAssignment']); // Kumpul Tugas
    Route::post('/grades', [AcademicController::class, 'giveGrade']);               // Beri Nilai

    // --- 4. QUIZ SYSTEM (Quiz, Question, Attempt, Answer) ---
    Route::prefix('quizzes')->group(function () {
        Route::post('/', [QuizController::class, 'storeQuiz']);                     // Buat Quiz
        Route::post('/{id}/questions', [QuizController::class, 'addQuestion']);     // Tambah Pertanyaan
        Route::post('/{id}/attempt', [QuizController::class, 'startAttempt']);      // Mulai Kerjakan Quiz
        Route::post('/save-answer', [QuizController::class, 'saveAnswer']);         // Simpan Jawaban per nomor
    });

    // --- 5. FORUM & COMMUNITY (Discussion, Reply) ---
    Route::prefix('discussions')->group(function () {
        Route::post('/', [ForumController::class, 'storeThread']);                  // Buat Diskusi Baru
        Route::post('/{id}/reply', [ForumController::class, 'reply']);              // Balas Diskusi
    });

    // --- 6. TRACKING & PROGRESS (ActivityLog, View, Progress) ---
    Route::prefix('tracking')->group(function () {
        Route::get('/my-logs', [TrackingController::class, 'getMyActivityLogs']);   // Lihat Log milik sendiri
        Route::post('/log', [TrackingController::class, 'logActivity']);            // Simpan Log baru
        Route::post('/progress/{materialId}', [TrackingController::class, 'trackProgress']); // Update progres materi
    });

    // --- 7. AI ANALYSIS ---
    Route::prefix('ai')->group(function () {
        Route::get('/analysis/{courseId}', [AiAnalysisController::class, 'getAnalysis']); // Ambil hasil rekomendasi AI
        Route::post('/analysis', [AiAnalysisController::class, 'storeAnalysis']);         // Simpan hasil analisis AI
    });

});
