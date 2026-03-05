<?php
use Illuminate\Support\Facades\Route;

Route::prefix('accounts')->name('accounts.')->group(function () {
    Route::livewire('/', 'accounting::account-list')->name('index');
});

Route::prefix('journals')->name('journals.')->group(function () {
    Route::livewire('/', 'accounting::journal-list')->name('index');
    Route::livewire('/{id}', 'accounting::journal-detail')->name('detail');
});