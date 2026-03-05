<?php

use Illuminate\Support\Facades\Route;

Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::livewire('/', 'purchasing::purchase-list')->name('index');
    Route::livewire('/create', 'purchasing::purchase-form')->name('create');
    Route::livewire('/{id}/edit', 'purchasing::purchase-form')->name('edit');
});

