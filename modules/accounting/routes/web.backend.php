<?php
use Illuminate\Support\Facades\Route;

Route::prefix('accounts')->name('accounts.')->group(function () {
    Route::livewire('/', 'accounting::account-list')->name('index');
    Route::livewire('/{id}', 'accounting::account-detail')->name('show');
});

Route::prefix('journals')->name('journals.')->group(function () {
    Route::livewire('/', 'accounting::journal-list')->name('index');
    Route::livewire('/{id}', 'accounting::journal-detail')->name('detail');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::livewire('/income-statement', 'accounting::income-statement')->name('income-statement');
});

Route::prefix('expenses')->name('expenses.')->group(function () {
    Route::livewire('/', 'accounting::expense-list')->name('index');
    Route::livewire('/create', 'accounting::expense-form')->name('create');
});