<?php

use Illuminate\Support\Facades\Route;
use Modules\Community\App\Http\Controllers\CommunityController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('academies', CommunityController::class)->names('Community');
});
