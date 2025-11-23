<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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
    Route::post('/simulations/{simulation}/snapshot', [SimulationController::class, 'snapshot'])->name('simulations.snapshot');

    // Settings
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('settings.profile');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('settings.destroy');
    
    // Tutorial route
    Route::post('/tutorial/complete', [DashboardController::class, 'completeTutorial'])->name('tutorial.complete');
});

require __DIR__.'/auth.php';
