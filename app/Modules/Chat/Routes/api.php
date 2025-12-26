<?php

use App\Modules\Chat\Http\Controllers;
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
Route::controller(Controllers\ChatController::class)->group(function () {
    Route::middleware(['auth:profiles', 'profile.cache'])->group(function () {
        Route::get('/chats', 'index');
        Route::post('/messages/{recipientId}', 'sendMessage');
        Route::get('/messages/{recipientId}', 'getMessages');
        Route::post('read/messages', 'readMessages');
    });

    Route::prefix('esb')->group(function () {
        Route::post('/messages', 'getMessageFromESB');
    });
});

