<?php

use App\Modules\Ai\Http\Controllers;
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
Route::post('/ai/request', [Controllers\AIController::class, 'getPredict']);
Route::get('/ai/chat_history', [Controllers\AIController::class, 'getChatHistory']);

