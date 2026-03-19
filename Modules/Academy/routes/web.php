<?php

use Illuminate\Support\Facades\Route;
use Modules\Academy\App\Http\Controllers\AcademyController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('academies', AcademyController::class)->names('academy');
});
