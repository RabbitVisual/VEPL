<?php

use Illuminate\Support\Facades\Route;
use Modules\NepeSearch\App\Http\Controllers\NepeSearchController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('nepesearches', NepeSearchController::class)->names('nepesearch');
});
