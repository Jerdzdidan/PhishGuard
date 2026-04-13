<?php

use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\LectureController;
use App\Http\Controllers\Admin\ScenarioController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\User\AssessmentController;
use App\Http\Controllers\User\CourseAppController;
use App\Http\Controllers\User\SectionEnrollmentController;
use App\Http\Controllers\User\SimulationController;
use App\Http\Controllers\User\UserLessonController;
use App\Http\Controllers\User\UserQuizController;
use App\Http\Controllers\User\CertificateController;
use Illuminate\Support\Facades\Route;


// Serve storage files through Laravel (bypasses Apache/symlink 403)
Route::get('/file/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.serve');

Route::get('/placeholder', function () {
    return 'This page is under construction.';
})->name('#');

// LANDING PAGE
Route::get('', [AuthController::class, 'index'])->name('landing.index');

// AUTH ROUTES
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google');
    Route::get('google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

    Route::get('sign-in', [AuthController::class, 'signin'])->name('sign-in');
    Route::post('login', [AuthController::class, 'authenticate'])->name('authenticate');

    Route::get('sign-up', [AuthController::class, 'signup'])->name('sign-up');
    Route::post('create-user', [AuthController::class, 'store'])->name('create-user');

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

// MAIN USER ROUTES
Route::prefix('')->middleware('auth')->group(function () {
    Route::get('home', [CourseAppController::class, 'index'])->name('home');
    Route::get('user/home', [CourseAppController::class, 'index'])->name('user.home');

    // Section Enrollment
    Route::get('sections/join', [SectionEnrollmentController::class, 'showJoin'])->name('sections.join');
    Route::post('sections/join', [SectionEnrollmentController::class, 'join']);
    Route::delete('sections/{id}/leave', [SectionEnrollmentController::class, 'leave'])->name('sections.leave');

    // Assessments
    Route::get('assessment/pre/{sectionId}', [AssessmentController::class, 'showPreAssessment'])->name('assessment.pre');
    Route::get('assessment/post/{sectionId}', [AssessmentController::class, 'showPostAssessment'])->name('assessment.post');
    Route::post('assessment/submit', [AssessmentController::class, 'submit'])->name('assessment.submit');
    Route::get('assessment/results/{attemptId}', [AssessmentController::class, 'results'])->name('assessment.results');

    // Certificate Routes
    Route::prefix('certificate')->name('certificate.')->group(function() {
        Route::get('check', [CertificateController::class, 'checkEligibility'])->name('check');
        Route::get('view', [CertificateController::class, 'view'])->name('view');
        Route::get('download', [CertificateController::class, 'download'])->name('download');
        Route::get('generate', [CertificateController::class, 'generate'])->name('generate');
        Route::post('send-email', [CertificateController::class, 'sendEmail'])->name('send-email');
    });

    Route::prefix('lessons')->name('lessons.')->group(function () {
        Route::get('show/{id}', [UserLessonController::class, 'show'])->name('show');
        Route::get('lecture/{id}/download', [UserLessonController::class, 'downloadLecture'])->name('lecture.download');

        Route::prefix('quiz')->name('quiz.')->group(function() {
            Route::get('{id}', [UserQuizController::class, 'show'])->name('show');
            Route::post('submit/{id}', [UserQuizController::class, 'submit'])->name('submit');
            Route::get('results/{id}/{attempt}', [UserQuizController::class, 'results'])->name('results');
            Route::get('retake/{id}', [UserQuizController::class, 'retake'])->name('retake');
        });

        Route::prefix('simulations')->name('simulations.')->group(function() {
            Route::get('{id}', [SimulationController::class, 'index'])->name('index');
            Route::get('{id}/{simId}', [SimulationController::class, 'show'])->name('show');
            Route::post('{id}/{simId}/start', [SimulationController::class, 'start'])->name('start');
            Route::post('{id}/{simId}/submit', [SimulationController::class, 'submit'])->name('submit');
            Route::get('{id}/{simId}/results/{attempt}', [SimulationController::class, 'results'])->name('results');
            Route::get('{id}/{simId}/retake', [SimulationController::class, 'retake'])->name('retake');
        });
    });
});

// ADMIN ROUTES (accessible by ADMIN and TEACHER)
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('home', function () {
        return view('admin.home.index');
    })->name('home');

    // LESSONS
    Route::prefix('lessons')->name('lessons.')->group(function () {
        Route::get('', [LessonController::class, 'index'])->name('index');

        Route::post('store', [LessonController::class, 'store'])->name('store');

        Route::get('edit/{id}', [LessonController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [LessonController::class, 'update'])->name('update');

        Route::get('destroy/{id}', [LessonController::class, 'destroy'])->name('destroy');

        Route::prefix('quiz')->name('quiz.')->group(function () {
            Route::get('{id}', [QuizController::class, 'show'])->name('show');
            Route::post('store/{id}', [QuizController::class, 'store'])->name('store');
        });

        // Question management - Admin only (teachers cannot manage questions)
        Route::prefix('question')->name('question.')->group(function () {
            Route::post('store/{id}', [QuizController::class, 'storeQuestion'])->name('store');
            Route::get('edit/{id}', [QuizController::class, 'editQuestion'])->name('edit');
            Route::put('update/{id}', [QuizController::class, 'updateQuestion'])->name('update');
            Route::delete('destroy/{id}', [QuizController::class, 'destroyQuestion'])->name('destroy');
            Route::post('re-order', [QuizController::class, 'reorderQuestions'])->name('reorder');
        });

        // Simulation management within lessons
        Route::prefix('simulation')->name('simulation.')->group(function () {
            Route::get('{id}', [LessonController::class, 'simulation'])->name('show');
            Route::post('{id}/toggle', [LessonController::class, 'toggleSimulation'])->name('toggle');
        });
    });

    // LECTURES (file uploads per lesson)
    Route::prefix('lectures')->name('lectures.')->group(function () {
        Route::get('{lessonId}', [LectureController::class, 'index'])->name('index');
        Route::post('{lessonId}', [LectureController::class, 'store'])->name('store');
        Route::get('download/{id}', [LectureController::class, 'download'])->name('download');
        Route::delete('{id}', [LectureController::class, 'destroy'])->name('destroy');
    });

    // SECTIONS
    Route::prefix('sections')->name('sections.')->group(function () {
        Route::get('', [SectionController::class, 'index'])->name('index');
        Route::get('data', [SectionController::class, 'getData'])->name('data');
        Route::post('store', [SectionController::class, 'store'])->name('store');
        Route::get('edit/{id}', [SectionController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [SectionController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [SectionController::class, 'destroy'])->name('destroy');

        // Student management within sections
        Route::get('{id}/students', [SectionController::class, 'students'])->name('students');
        Route::get('{id}/students/data', [SectionController::class, 'getStudentsData'])->name('students.data');
        Route::post('{id}/students/add', [SectionController::class, 'addStudent'])->name('students.add');
        Route::delete('{id}/students/{studentId}', [SectionController::class, 'removeStudent'])->name('students.remove');
        Route::get('{id}/students/available', [SectionController::class, 'availableStudents'])->name('students.available');

        // Section-Lesson management
        Route::get('{id}/lessons', [SectionController::class, 'manageLessons'])->name('lessons');
        Route::post('{id}/lessons/add', [SectionController::class, 'addLesson'])->name('lessons.add');
        Route::delete('{id}/lessons/{lessonId}', [SectionController::class, 'removeLesson'])->name('lessons.remove');
        Route::get('{id}/lessons/available', [SectionController::class, 'availableLessons'])->name('lessons.available');
    });

    // SCENARIOS
    Route::prefix('scenarios')->name('scenarios.')->group(function () {
        Route::get('', [ScenarioController::class, 'index'])->name('index');
        Route::get('data', [ScenarioController::class, 'getData'])->name('data');
        Route::post('store', [ScenarioController::class, 'store'])->name('store');
        Route::get('edit/{id}', [ScenarioController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [ScenarioController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [ScenarioController::class, 'destroy'])->name('destroy');
        Route::post('toggle/{id}', [ScenarioController::class, 'toggle'])->name('toggle');

        // Scenario Items
        Route::get('{id}/items', [ScenarioController::class, 'items'])->name('items');
        Route::get('{id}/items/data', [ScenarioController::class, 'getItemsData'])->name('items.data');
        Route::post('{id}/items/store', [ScenarioController::class, 'storeItem'])->name('items.store');
        Route::get('items/edit/{id}', [ScenarioController::class, 'editItem'])->name('items.edit');
        Route::put('items/update/{id}', [ScenarioController::class, 'updateItem'])->name('items.update');
        Route::delete('items/destroy/{id}', [ScenarioController::class, 'destroyItem'])->name('items.destroy');
        Route::post('items/reorder', [ScenarioController::class, 'reorderItems'])->name('items.reorder');
    });

    // USER MANAGEMENT
    Route::prefix('users')->name('users.')->group(function() {
        Route::get('', [UserController::class, 'index'])->name('index');

        Route::get('data', [UserController::class, 'getData'])->name('data');
        Route::get('stats', [UserController::class, 'getStats'])->name('stats');
        
        Route::post('store', [UserController::class, 'store'])->name('store');

        Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [UserController::class, 'update'])->name('update');

        Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('destroy');

        Route::post('toggle/{id}', [UserController::class, 'toggle'])->name('toggle');
    });

    Route::prefix('analytics')->name('analytics.')->group(function() {
        Route::get('overview', [AnalyticsController::class, 'overview'])->name('overview');
        Route::get('quiz', [AnalyticsController::class, 'quizAnalytics'])->name('quiz');
        Route::get('simulation', [AnalyticsController::class, 'simulationAnalytics'])->name('simulation');
        Route::get('assessment', [AnalyticsController::class, 'assessmentAnalytics'])->name('assessment');
        Route::get('heatmap', [AnalyticsController::class, 'heatmap'])->name('heatmap');
        Route::get('export', [AnalyticsController::class, 'export'])->name('export');
    });

    // USER PROGRESS
    Route::prefix('user-progress')->name('user-progress.')->group(function() {
        Route::get('', [App\Http\Controllers\Admin\UserProgressController::class, 'index'])->name('index');
        Route::get('data', [App\Http\Controllers\Admin\UserProgressController::class, 'getData'])->name('data');
        Route::get('show/{id}', [App\Http\Controllers\Admin\UserProgressController::class, 'show'])->name('show');
    });
});

