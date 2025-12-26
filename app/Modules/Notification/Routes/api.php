<?php

use App\Modules\Notification\Http\Controllers;
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
Route::get('/notifications', [Controllers\NotificationController::class, 'count']);
Route::get('/notifications/list', [Controllers\NotificationController::class, 'index']);

Route::post('/push/subscribe', [Controllers\NotificationController::class, 'subscribe']);
Route::delete('/push/subscribe', [Controllers\NotificationController::class, 'unsubscribe']);

Route::get('/push/test', [Controllers\NotificationController::class, 'sendTestNotification']);
