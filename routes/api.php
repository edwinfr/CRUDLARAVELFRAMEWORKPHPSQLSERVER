<?php 

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::resource('posts', PostController::class);
});