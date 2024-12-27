<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::middleware("auth:sanctum")->group(function() {
        Route::get('/user', 'userProfile');
        Route::get('/logout', 'userLogout');
    });
});
