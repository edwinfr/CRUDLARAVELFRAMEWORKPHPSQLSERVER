<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Api\PostController as ApiPostController;

// Inicio de Laravel
Route::get('/', function () {
    return view('index');
});

// CRUD de artículos (vista)
Route::get('/articulos', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/list', [PostController::class, 'list'])->name('posts.list');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

// API REST (sin CSRF)
Route::prefix('api')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->group(function () {
        Route::apiResource('posts', ApiPostController::class);
    });
