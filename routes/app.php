<?php

use App\Http\Controllers\App\AppController;
use Illuminate\Support\Facades\Route;

// app api
Route::middleware(['auth.app'])->group(function () {
    Route::post('/init', [AppController::class, 'init']);
    Route::post('/refresh', [AppController::class, 'refresh']);
    Route::post('/tag', [AppController::class, 'tag']);
});
