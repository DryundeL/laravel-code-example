<?php

use App\Modules\App\Http\Controllers\MaintenanceController;
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
Route::controller(MaintenanceController::class)->group(function () {
    Route::get('/maintenance', 'getStatus');
    Route::post('/maintenance', 'changeStatus');
});

