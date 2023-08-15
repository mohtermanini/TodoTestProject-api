<?php

use App\Http\Controllers\Api\V1\User\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\User\TodoListController;

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

Route::controller(AuthController::class)
    ->prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/', 'store')->name('store');
        Route::delete('/', 'destroy')->name('destroy')->middleware(['auth:sanctum']);
    });

Route::controller(UserController::class)
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('/', 'show')->name('show')->middleware(['auth:sanctum']);
    });

Route::controller(TodoListController::class)
    ->prefix('todolists')
    ->name('todolists.')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::patch('/{todolist}', 'update')->name('update');
        Route::delete('/{todolist}', 'destroy')->name('destroy');
    });

Route::controller(TaskController::class)
    ->prefix('todolists/{todolist}/tasks')
    ->name('todolists.tasks.')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{task}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::patch('/{task}', 'update')->name('update');
        Route::delete('/{task}', 'destroy')->name('destroy');
    });