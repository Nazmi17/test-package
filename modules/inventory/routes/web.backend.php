<?php

use Illuminate\Support\Facades\Route;

// Cukup pakai 'materials' saja karena 'inventory' sudah di-generate otomatis oleh Synapse
Route::prefix('materials')->name('materials.')->group(function () {
    
    Route::livewire('/', 'inventory::material-list')->name('index');
    Route::livewire('/create', 'inventory::material-form')->name('create');
    Route::livewire('/{id}/edit', 'inventory::material-form')->name('edit');
    
});

Route::prefix('stock-ledgers')->name('stock-ledgers.')->group(function () {
    Route::livewire('/', 'inventory::stock-ledger-list')->name('index');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::livewire('/', 'inventory::category-list')->name('index');
    Route::livewire('/create', 'inventory::category-form')->name('create');
    Route::livewire('/{id}/edit', 'inventory::category-form')->name('edit');
});