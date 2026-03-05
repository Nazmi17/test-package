<?php
use Illuminate\Support\Facades\Route;

Route::prefix('labors')->name('labors.')->group(function () {
    Route::livewire('/', 'costing::labor-list')->name('index');
    Route::livewire('/create', 'costing::labor-form')->name('create');
    Route::livewire('/{id}/edit', 'costing::labor-form')->name('edit');
});

Route::prefix('overheads')->name('overheads.')->group(function () {
    Route::livewire('/', 'costing::overhead-list')->name('index');
    Route::livewire('/create', 'costing::overhead-form')->name('create');
    Route::livewire('/{id}/edit', 'costing::overhead-form')->name('edit');
});