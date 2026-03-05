<?php

use Illuminate\Support\Facades\Route;
use SynApps\Modules\Home\Controllers\HomeController;

Route::group([], function () {
    Route::get('/', [HomeController::class, 'index'])->name('landing');
});
