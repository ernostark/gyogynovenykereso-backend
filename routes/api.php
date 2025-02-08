<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::put('/profile', 'updateProfile')->middleware('auth:sanctum');
    Route::get('/getprofile', 'getProfile')->middleware('auth:sanctum');
});

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::controller(PostController::class)->group(function () {
        Route::get('/posts', 'index');
        Route::get('/posts/{id}', 'show');
        Route::post('/posts', 'store');
        Route::post('/posts/{id}', 'update');
        Route::delete('/posts/{id}', 'destroy');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::post('/categories', 'store');
        Route::delete('/categories/{id}', 'destroy');
    });
    Route::controller(ContactController::class)->group(function () {
        Route::get('/messages', 'index');        
        Route::put('/messages/{id}/read', 'markAsRead');        
    });
});

Route::get('posts/featured', [PostController::class, 'getFeaturedPosts']);
Route::get('posts/latest', [PostController::class, 'getLatestPosts']);
Route::post('posts/search-by-diseases', [PostController::class, 'searchByDiseases']);
Route::get('posts/search', [PostController::class, 'searchInContent']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/posts/{id}/check-access', [PostController::class, 'checkAccess']);
Route::get('/posts/latest', [PostController::class, 'getLatestPosts']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/posts', [CategoryController::class, 'getPosts']);
Route::post('/contact', [ContactController::class, 'store']);