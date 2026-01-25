<?php

use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\User\CourseAppController;
use App\Http\Controllers\User\SimulationController;
use App\Http\Controllers\User\UserLessonController;
use App\Http\Controllers\User\UserQuizController;
use Illuminate\Support\Facades\Route;


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
    Route::get('home', [UserLessonController::class, 'index'])->name('user.home');

    Route::prefix('lessons')->name('lessons.')->group(function () {
        Route::get('show/{id}', [UserLessonController::class, 'show'])->name('show');

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
        });
    });
});

// ADMIN ROUTES
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

        Route::prefix('quiz')->name('quiz.')->group(function () {
            Route::get('{id}', [QuizController::class, 'show'])->name('show');
            Route::post('store/{id}', [QuizController::class, 'store'])->name('store');
        });

        Route::prefix('question')->name('question.')->group(function () {
            Route::post('store/{id}', [QuizController::class, 'storeQuestion'])->name('store');
            Route::get('edit/{id}', [QuizController::class, 'editQuestion'])->name('edit');
            Route::put('update/{id}', [QuizController::class, 'updateQuestion'])->name('update');
            Route::delete('destroy/{id}', [QuizController::class, 'destroyQuestion'])->name('destroy');
            Route::post('re-order', [QuizController::class, 'reorderQuestions'])->name('reorder');
        });
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
});