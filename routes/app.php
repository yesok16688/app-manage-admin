<?php

use Illuminate\Support\Facades\Route;

// app api
Route::middleware(['auth.app'])->post('/init', [\App\Http\Controllers\App\AppController::class, 'init']);
