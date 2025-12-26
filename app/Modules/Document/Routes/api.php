<?php

use App\Modules\Document\Http\Controllers;
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
Route::controller(Controllers\DocumentController::class)->group(function () {
    Route::middleware(['time.restriction', 'profile.cache'])->group(function () {
        Route::prefix('documents')->group(function () {
            Route::get('/', 'index');
            Route::get('/types', 'getRequestTypes');
            Route::get('/types/{requestType}', 'getRequestType');
            Route::get('/ticket', 'getTicket');
        });
    });

    Route::prefix('documents')->group(function () {
        Route::get('/zbook', 'getZbook');
        Route::get('/qr-code', 'generateQrCode');
    });

    Route::prefix('templates')->group(function () {
        Route::get('/documents', 'getDocumentsTemplates');
    });
});

Route::controller(Controllers\DocumentDataController::class)->group(function () {
    Route::middleware(['time.restriction', 'profile.cache'])->group(function () {
        Route::prefix('file')->group(function () {
            Route::get('/documents/{documentId}', 'getDocumentFileData');
            Route::get('/requests/{requestId}', 'getRequestFileData');
        });

        Route::prefix('sign')->group(function () {
            Route::post('/documents/{documentId}', 'signDocument');
            Route::post('/requests/{requestId}', 'signRequest');
        });

        Route::prefix('confirm')->group(function () {
            Route::post('/requests/{requestId}', 'confirmRequest');
        });

        Route::prefix('reject')->group(function () {
            Route::post('/requests/{requestId}', 'rejectRequest');
        });

        Route::prefix('eds')->group(function () {
            Route::get('/file', 'getNewEdsFileData');
            Route::post('/send_code', 'sendCode');
            Route::post('/verify', 'sendNewEds');
        });

        Route::prefix('options')->group(function () {
            Route::get('/requests/{requestType}', 'getRequestOptions');
        });

        Route::prefix('requests')->group(function () {
            Route::post('/{requestType}', 'sendRequest');
        });
    });
});
