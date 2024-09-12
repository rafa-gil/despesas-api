<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\v1\ExpenseController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::group(['prefix' => 'v1'], function () {
        Route::apiResource('expenses', ExpenseController::class);
    });
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


