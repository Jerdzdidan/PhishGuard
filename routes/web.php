<?php

use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\User\CourseAppController;
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

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

// MAIN USER ROUTES
Route::prefix('')->middleware('auth')->group(function () {
    Route::get('home', [CourseAppController::class, 'index'])->name('user.home');
});

// ADMIN ROUTES
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('home', function () {
        return view('admin.home.index');
    })->name('home');

    // LESSONS

    Route::prefix('lessons')->name('lessons.')->group(function () {
        Route::get('', [LessonController::class, 'index'])->name('index');

        Route::get('edit/{id}', [LessonController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [LessonController::class, 'update'])->name('update');
    });
});