<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require base_path('modules/User/Presentation/Http/Routes/api.php');
    require base_path('modules/Order/Presentation/Http/Routes/api.php');
});
