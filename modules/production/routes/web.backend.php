<?php

use Illuminate\Support\Facades\Route;

Route::prefix('products')->name('products.')->group(function () {
    Route::livewire('/', 'production::product-list')->name('index');
    Route::livewire('/create', 'production::product-form')->name('create');
    Route::livewire('/{id}/edit', 'production::product-form')->name('edit');
});

Route::prefix('manufactures')->name('manufactures.')->group(function () {
    Route::livewire('/', 'production::manufacture-list')->name('index');
    Route::livewire('/create', 'production::manufacture-form')->name('create');
    Route::livewire('/{id}/edit', 'production::manufacture-form')->name('edit');
});