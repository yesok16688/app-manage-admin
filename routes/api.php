<?php

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // auth api
    Route::get('/info', [\App\Http\Controllers\Admin\AuthController::class, 'info']);
    Route::get('/refresh-token', [\App\Http\Controllers\Admin\AuthController::class, 'refreshToken']);
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout']);

    Route::apiResource('app', \App\Http\Controllers\Admin\AppController::class);
    Route::apiResource('redirect-url', \App\Http\Controllers\Admin\RedirectUrlController::class);

    // option api
    Route::get('/init', [\App\Http\Controllers\Admin\OptionController::class, 'getOptions']);
});
