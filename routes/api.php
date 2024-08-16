<?php

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\RedirectUrlController;
use App\Http\Controllers\Admin\RegionBlacklistController;
use App\Http\Controllers\Admin\RegionWhitelistController;
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
    Route::apiResource('redirect-url', RedirectUrlController::class);
    Route::apiResource('region-blacklist', RegionBlacklistController::class);
    Route::apiResource('region-whitelist', RegionWhitelistController::class);

    // option api
    Route::get('/init', [OptionController::class, 'getOptions']);
    Route::get('/sub-region-options/{region_code}', [OptionController::class, 'getSubRegionOptions']);
});
