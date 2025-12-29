<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'role:admin,manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Manager/Dashboard');
    })->name('dashboard');

    Route::get('/reports', function () {
        return Inertia::render('Manager/Reports');
    })->name('reports');
});
