<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GradeController;
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
            Route::post('/', [CourseContentController::class, 'store']);
            Route::post('/{courseId}/modules', [CourseContentController::class, 'addModule']);
        });
        Route::post('/modules/{moduleId}/materials', [CourseContentController::class, 'addMaterial']);

        // Assignment & penilaian dibuat oleh teacher
        Route::post('/assignments', [AssignmentController::class, 'store']);
        Route::post('/grades', [GradeController::class, 'store']);

        // Quiz dibuat oleh teacher
        Route::prefix('quizzes')->group(function () {
            Route::post('/', [QuizController::class, 'storeQuiz']);
            Route::post('/{id}/questions', [QuizController::class, 'addQuestion']);
        });
    });

    // --- 3. STUDENT ONLY ---
    Route::middleware('role:student')->group(function () {
        Route::post('/enroll', [EnrollmentController::class, 'store']);
        Route::post('/assignments/{id}/submit', [AssignmentController::class, 'submit']);

        Route::prefix('quizzes')->group(function () {
            Route::post('/{id}/attempt', [QuizController::class, 'startAttempt']);
            Route::post('/save-answer', [QuizController::class, 'saveAnswer']);
        });
    });

    // --- 4. FORUM (semua role) ---
    Route::prefix('discussions')->group(function () {
        Route::post('/', [ForumController::class, 'storeThread']);
        Route::post('/{id}/reply', [ForumController::class, 'reply']);
    });

    // --- 5. TRACKING & PROGRESS (semua role) ---
    Route::prefix('tracking')->group(function () {
        Route::get('/my-logs', [TrackingController::class, 'getActivityLogs']);
        Route::post('/log', [TrackingController::class, 'logActivity']);
        Route::post('/progress/{materialId}', [TrackingController::class, 'trackProgress']);
    });

    // --- 6. AI ANALYSIS (semua role) ---
    Route::prefix('ai')->group(function () {
        Route::get('/analysis/{courseId}', [AiAnalysisController::class, 'getAnalysis']);
        Route::post('/analysis', [AiAnalysisController::class, 'storeAnalysis']);
    });
});
