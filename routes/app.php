<?php

use App\Http\Controllers\App\AppController;
use App\Http\Controllers\App\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function() {
    return json_encode(['code' => 0, 'msg' => 'OK']);
});
// app api
Route::middleware(['auth.app'])->group(function () {
    Route::post('/init', [AppController::class, 'init']);
    Route::post('/refresh', [AppController::class, 'refresh']);
    Route::post('/tag', [AppController::class, 'tag']);
    Route::post('/eventpost', [EventController::class, 'report']);
});
