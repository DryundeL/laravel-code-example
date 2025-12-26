<?php

use App\Modules\User\Http\Controllers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::controller(Controllers\UserController::class)->group(function () {
    Route::post('/sso/users', 'store');
    Route::post('/user/update', 'update');
});
Route::controller(Controllers\ModuleController::class)->group(function () {
    Route::get('/modules', 'index');
    Route::post('/modules', 'store');
    Route::post('/modules/{module}/delete', 'destroy');
    Route::post('/modules/{module}/swap', 'swap');
});
Route::get('/calendar/{token}', [Controllers\CalendarController::class, 'getCalendar']);

Route::middleware('auth:profiles')->group(function () {
    Route::controller(Controllers\ProfileController::class)->group(function () {
        Route::get('/profiles', 'index')->middleware('profile.cache');
        Route::post('/profiles/{profile}/switch', 'switch')->middleware('profile.cache');
        Route::get('/profile', 'show');
    });

    Route::get('/teachers/{id}', [Controllers\TeacherController::class, 'getTeacherById']);
    Route::post('/settings', [Controllers\SettingsController::class, 'store'])->middleware(['profile.cache', 'debt.check:2']);
    Route::get('/calendar', [Controllers\CalendarController::class, 'getCalendarLink'])->middleware(['profile.cache', 'debt.check:2']);
});

Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/accesses', [Controllers\UserController::class, 'getAccesses']);
});
