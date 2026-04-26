<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
*/

Route::middleware('auth:sanctum')->group(function () {

    // --- 1. USER & PROFILE (semua role) ---
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'show']);
        Route::put('/', [UserController::class, 'update']);
    });

    // --- 2. COURSE & CONTENT (hanya teacher) ---
    Route::middleware('role:teacher')->group(function () {
        Route::prefix('courses')->group(function () {
            Route::post('/', [CourseContentController::class, 'storeCourse']);
            Route::post('/{courseId}/modules', [CourseContentController::class, 'addModule']);
        });
        Route::post('/modules/{moduleId}/materials', [CourseContentController::class, 'addMaterial']);

        // Assignment dibuat oleh teacher
        Route::post('/assignments', [AcademicController::class, 'storeAssignment']);

        // Penilaian oleh teacher
        Route::post('/grades', [AcademicController::class, 'giveGrade']);

        // Quiz dibuat oleh teacher
        Route::prefix('quizzes')->group(function () {
            Route::post('/', [QuizController::class, 'storeQuiz']);
            Route::post('/{id}/questions', [QuizController::class, 'addQuestion']);
        });
    });

    // --- 3. STUDENT ONLY ---
    Route::middleware('role:student')->group(function () {
        // Enrollment & submit tugas hanya oleh siswa
        Route::post('/enroll', [AcademicController::class, 'enroll']);
        Route::post('/assignments/{id}/submit', [AcademicController::class, 'submitAssignment']);

        // Kerjakan quiz hanya oleh siswa
        Route::prefix('quizzes')->group(function () {
            Route::post('/{id}/attempt', [QuizController::class, 'startAttempt']);
            Route::post('/save-answer', [QuizController::class, 'saveAnswer']);
        });
    });

    // --- 4. FORUM (semua role boleh) ---
    Route::prefix('discussions')->group(function () {
        Route::post('/', [ForumController::class, 'storeThread']);
        Route::post('/{id}/reply', [ForumController::class, 'reply']);
    });

    // --- 5. TRACKING & PROGRESS (semua role boleh) ---
    Route::prefix('tracking')->group(function () {
        Route::get('/my-logs', [TrackingController::class, 'getActivityLogs']);
        Route::post('/log', [TrackingController::class, 'logActivity']);
        Route::post('/progress/{materialId}', [TrackingController::class, 'trackProgress']);
    });

    // --- 6. AI ANALYSIS (semua role boleh) ---
    Route::prefix('ai')->group(function () {
        Route::get('/analysis/{courseId}', [AiAnalysisController::class, 'getAnalysis']);
        Route::post('/analysis', [AiAnalysisController::class, 'storeAnalysis']);
    });

});