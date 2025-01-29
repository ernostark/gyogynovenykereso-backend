<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register'); // Felhasználói regisztráció
    Route::post('/login', 'login'); // Felhasználói bejelentkezés
    Route::post('/logout', 'logout')->middleware('auth:sanctum'); // Kijelentkezés
    Route::put('/profile', 'updateProfile')->middleware('auth:sanctum'); // Profil frissítése
    Route::get('/getprofile', 'getProfile')->middleware('auth:sanctum'); // Profil adatainak lekérése
});

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::controller(PostController::class)->group(function () {
        Route::get('/posts', 'index'); // Bejegyzések listázása
        Route::get('/posts/{id}', 'show'); // Egyedi bejegyzés megtekintése
        Route::post('/posts', 'store'); // Új bejegyzés létrehozása
        Route::put('/posts/{id}', 'update'); // Bejegyzés frissítése
        Route::delete('/posts/{id}', 'destroy'); // Bejegyzés törlése
    });
});
