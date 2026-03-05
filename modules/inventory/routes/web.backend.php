<?php

use Illuminate\Support\Facades\Route;

// Cukup pakai 'materials' saja karena 'inventory' sudah di-generate otomatis oleh Synapse
Route::prefix('materials')->name('materials.')->group(function () {
    
    Route::livewire('/', 'inventory::material-list')->name('index');
    Route::livewire('/create', 'inventory::material-form')->name('create');
    Route::livewire('/{id}/edit', 'inventory::material-form')->name('edit');
    
});