<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::put('/profile', 'updateProfile')->middleware('auth:sanctum');
    Route::get('/getprofile', 'getProfile')->middleware('auth:sanctum');
});