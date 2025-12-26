<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticsController;

Route::get('/docs/api', static function () {
    return view('docs.api');
});

Route::get('/' . hash('md5', 'analytics'), [StatisticsController::class, 'index'])->name('statistics.index');
