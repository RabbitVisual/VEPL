<?php

use Illuminate\Support\Facades\Route;
use Modules\NepeSearch\Http\Controllers\NepeSearchController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nepesearches', NepeSearchController::class)->names('nepesearch');
});
