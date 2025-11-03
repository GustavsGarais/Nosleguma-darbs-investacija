<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Simulation routes
    Route::resource('simulations', SimulationController::class);
    Route::post('/simulations/{simulation}/run', [SimulationController::class, 'run'])->name('simulations.run');
});

require __DIR__.'/auth.php';
