<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/quick-tour', function () {
    return view('pages.quick-tour');
})->name('quick-tour');

Route::post('/language', [LanguageController::class, 'switch'])->name('language.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('simulations.index');
    })->name('dashboard');
    
    // Simulation routes
    Route::resource('simulations', SimulationController::class);
    Route::post('/simulations/{simulation}/run', [SimulationController::class, 'run'])->name('simulations.run');
    Route::post('/simulations/{simulation}/snapshot', [SimulationController::class, 'snapshot'])->name('simulations.snapshot');

    // Settings
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('settings.profile');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('settings.destroy');
    
    // Two-Factor Authentication
    Route::get('/settings/two-factor', [\App\Http\Controllers\TwoFactorController::class, 'show'])->name('settings.two-factor');
    Route::post('/settings/two-factor/enable', [\App\Http\Controllers\TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/settings/two-factor/disable', [\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/settings/two-factor/recovery-codes', [\App\Http\Controllers\TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes');
    
    // Support tickets (user-facing)
    Route::get('/reports', [SupportTicketController::class, 'create'])->name('tickets.create');
    Route::post('/reports', [SupportTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
    
    // Tutorial route
    Route::post('/tutorial/complete', [DashboardController::class, 'completeTutorial'])->name('tutorial.complete');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Support tickets management
    Route::get('/tickets', [AdminSupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminSupportTicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}/status', [AdminSupportTicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::delete('/tickets/{ticket}', [AdminSupportTicketController::class, 'destroy'])->name('tickets.delete');
});

require __DIR__.'/auth.php';
