<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\AppVersionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\AppUrlController;
use App\Http\Controllers\Admin\RegionBlacklistController;
use App\Http\Controllers\Admin\RegionWhitelistController;
use App\Http\Controllers\Admin\UrlHandleLogController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // auth api
    Route::get('/info', [AuthController::class, 'info']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('app', AppController::class);
    Route::apiResource('app-version', AppVersionController::class);
    Route::apiResource('app-url', AppUrlController::class);
    Route::apiResource('region-blacklist', RegionBlacklistController::class);
    Route::apiResource('region-whitelist', RegionWhitelistController::class);

    Route::get('url-handle-log', [UrlHandleLogController::class, 'index']);
    Route::get('url-handle-log/{id}', [UrlHandleLogController::class, 'show']);
    Route::delete('url-handle-log', [UrlHandleLogController::class, 'destroy']);
    Route::post('url-handle/{id}', [UrlHandleLogController::class, 'handle']);

    Route::post('upload/icon', [FileController::class, 'uploadIcon']);
    Route::post('upload/img', [FileController::class, 'uploadImage']);
    //Route::get('image/{id}', [FileController::class, 'image']);

    // option api
    Route::get('/init', [OptionController::class, 'getOptions']);
    Route::get('/sub-region-options/{region_code}', [OptionController::class, 'getSubRegionOptions']);
});
