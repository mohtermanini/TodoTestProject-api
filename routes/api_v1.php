<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Http\Request;
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

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    Route::post('/', 'store')->name('store');
    Route::delete('/', 'destroy')->name('destroy')->middleware(['auth:sanctum']);
});

Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
    Route::post('/', 'store')->name('store');
    Route::get('/', 'show')->name('show')->middleware(['auth:sanctum']);
});