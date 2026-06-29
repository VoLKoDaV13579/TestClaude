<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Order\Presentation\Http\Controllers\OrderController;

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show'])->where('id', '[0-9a-f\-]{36}');
    Route::get('/user/{userId}', [OrderController::class, 'byUser'])->where('userId', '[0-9a-f\-]{36}');
});
