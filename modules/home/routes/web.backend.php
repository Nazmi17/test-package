<?php

use Illuminate\Support\Facades\Route;
use SynApps\Modules\Home\Controllers\HomeController;

Route::group([], function () {
    Route::get('/', function () {
        return redirect(backend_route(config('synapps.auth.routes.after_login')));
    });
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});
