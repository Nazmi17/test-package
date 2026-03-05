<?php
use Illuminate\Support\Facades\Route;

Route::prefix('sales')->name('sales.')->group(function () {
    Route::livewire('/', 'sales::sale-list')->name('index');
    Route::livewire('/create', 'sales::sale-form')->name('create');
    Route::livewire('/{id}/edit', 'sales::sale-form')->name('edit');
});