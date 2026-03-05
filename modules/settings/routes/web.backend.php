<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::livewire('configuration', 'settings.configuration-list')
        ->name('configuration');

    Route::livewire('languages', 'settings.languages-list')
        ->name('languages');
});
