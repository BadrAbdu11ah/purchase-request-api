<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\dashboardController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth.api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('orders')->group(function () {
        Route::post('/store', [OrderController::class, 'store'])
            ->middleware('role:store_keeper');

        Route::get('/', [OrderController::class, 'index'])
            ->middleware('role:purchasing_officer,store_keeper');

        Route::post('/update-status/{id}', [OrderController::class, 'updateStatus'])
            ->middleware('role:purchasing_officer,store_keeper');

        Route::get('/show/{id}', [OrderController::class, 'show'])
            ->middleware('role:purchasing_officer,store_keeper');
        Route::delete('/delete/{id}', [OrderController::class, 'destroy'])
            ->middleware('role:store_keeper');
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->middleware('role:store_keeper,purchasing_officer');

        Route::post('/store', [CategoryController::class, 'store'])
            ->middleware('role:store_keeper');

        Route::get('/show/{id}', [CategoryController::class, 'show'])
            ->middleware('role:store_keeper,purchasing_officer');

        Route::post('/update/{id}', [CategoryController::class, 'update'])
            ->middleware('role:store_keeper');

        Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])
            ->middleware('role:store_keeper');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->middleware('role:store_keeper,purchasing_officer');

        Route::post('/store', [ProductController::class, 'store'])
            ->middleware('role:store_keeper');

        Route::get('/show/{id}', [ProductController::class, 'show'])
            ->middleware('role:store_keeper,purchasing_officer');

        Route::post('/update/{id}', [ProductController::class, 'update'])
            ->middleware('role:store_keeper');

        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])
            ->middleware('role:store_keeper');
    });

    Route::get('/products-list', [OrderController::class, 'getProducts'])
        ->middleware('role:store_keeper,purchasing_officer');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:store_keeper,purchasing_officer');
    
});