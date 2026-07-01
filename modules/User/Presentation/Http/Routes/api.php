<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\User\Presentation\Http\Controllers\UserController;

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'register']);
    Route::get('/{id}', [UserController::class, 'show'])->where('id', '[0-9a-f\-]{36}');
});
