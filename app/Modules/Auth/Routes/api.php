<?php

use App\Modules\Auth\Http\Controllers;
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
Route::controller(Controllers\AuthController::class)->group(function () {
    Route::get('/user', 'getUser');
    Route::delete('/user/logout', 'logoutUserByEmail');
    Route::post('/profiles/{profile}', 'authByProfile');

    Route::middleware('auth:profiles')->group(function () {
        Route::delete('/logout', 'logout');
    });
});

