<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\AppEventController;
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

    Route::get('app', [AppController::class, 'index']);
    Route::get('app/{id}', [AppController::class, 'show']);
    Route::post('app', [AppController::class, 'store'])->middleware(['auth.2fa']);
    Route::put('app/{id}', [AppController::class, 'update'])->middleware(['auth.2fa']);
    Route::delete('app/{id}', [AppController::class, 'destroy']);

    Route::get('app-version', [AppVersionController::class, 'index']);
    Route::get('app-version/{id}', [AppVersionController::class, 'show']);
    Route::post('app-version', [AppVersionController::class, 'store'])->middleware(['auth.2fa']);
    Route::put('app-version/{id}', [AppVersionController::class, 'update'])->middleware(['auth.2fa']);
    Route::delete('app-version/{id}', [AppVersionController::class, 'destroy'])->middleware(['auth.2fa']);

    Route::get('app-url', [AppUrlController::class, 'index']);
    Route::get('app-url/{id}', [AppUrlController::class, 'show']);
    Route::post('app-url', [AppUrlController::class, 'store'])->middleware(['auth.2fa']);
    Route::put('app-url/{id}', [AppUrlController::class, 'update'])->middleware(['auth.2fa']);
    Route::delete('app-url/{id}', [AppUrlController::class, 'destroy'])->middleware(['auth.2fa']);

    Route::apiResource('app-event-log', AppEventController::class);
    //Route::apiResource('region-blacklist', RegionBlacklistController::class);
    //Route::apiResource('region-whitelist', RegionWhitelistController::class);

    Route::get('url-handle-log', [UrlHandleLogController::class, 'index']);
    Route::get('url-handle-log/{id}', [UrlHandleLogController::class, 'show']);

    Route::delete('url-handle-log/{id}', [UrlHandleLogController::class, 'destroy'])->middleware(['auth.2fa']);
    Route::post('url-handle/{id}', [UrlHandleLogController::class, 'handle'])->middleware(['auth.2fa']);

    Route::post('upload/icon', [FileController::class, 'uploadIcon']);
    Route::post('upload/img', [FileController::class, 'uploadImage']);
    //Route::get('image/{id}', [FileController::class, 'image']);

    // option api
    Route::get('/init', [OptionController::class, 'getOptions']);
    Route::get('/sub-region-options/{region_code}', [OptionController::class, 'getSubRegionOptions']);
});
