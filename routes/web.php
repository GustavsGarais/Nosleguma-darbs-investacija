<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\BlockedEmailController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Auth\ReportUnauthorizedPasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\SupportHubController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TwoFactorRecoveryRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/quick-tour', function () {
    return view('pages.quick-tour');
})->name('quick-tour');

Route::post('/language', [LanguageController::class, 'switch'])->name('language.switch');

// Support hub: tickets (auth) + public account / 2FA recovery
Route::get('/support', [SupportHubController::class, 'index'])
    ->middleware('throttle:two-factor-recovery-support-page')
    ->name('support.index');
Route::post('/support/recovery', [TwoFactorRecoveryRequestController::class, 'store'])
    ->middleware('throttle:two-factor-recovery-support-submit')
    ->name('support.recovery.store');
Route::get('/support/thanks', function () {
    return view('support.two-factor-recovery-thanks');
})->name('support.thanks');

Route::redirect('/reports', '/support', 302);

Route::get('/password-reset/report-unauthorized/{user}', ReportUnauthorizedPasswordResetController::class)
    ->middleware(['signed', 'throttle:12,1'])
    ->name('password.reset.report-unauthorized');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('simulations.index');
    })->name('dashboard');

    // Simulation routes
    Route::resource('simulations', SimulationController::class);
    Route::post('/simulations/{simulation}/snapshot', [SimulationController::class, 'snapshot'])->name('simulations.snapshot');
    Route::post('/simulations/{simulation}/runner-state', [SimulationController::class, 'runnerState'])->name('simulations.runner-state');

    // Settings
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('settings.profile');
    Route::patch('/settings/currency', [ProfileController::class, 'updateCurrency'])->name('settings.currency');
    Route::patch('/settings/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('settings.password');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('settings.destroy');

    // Two-Factor Authentication (setup / enable / disable / recovery codes)
    Route::get('/settings/two-factor', [\App\Http\Controllers\TwoFactorController::class, 'show'])->name('settings.two-factor');
    Route::post('/settings/two-factor', [\App\Http\Controllers\TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/settings/two-factor/disable', [\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/settings/two-factor/recovery-codes', [\App\Http\Controllers\TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes');

    // Support tickets (user-facing; form lives on /support)
    Route::post('/support/ticket', [SupportTicketController::class, 'store'])->name('support.ticket.store');
    Route::get('/tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');

    // Tutorial route
    Route::post('/tutorial/complete', [DashboardController::class, 'completeTutorial'])->name('tutorial.complete');

    // (Market feature removed)
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('audit.index');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/disable-2fa', [AdminController::class, 'disableTwoFactor'])->name('users.disableTwoFactor');

    // Email blocking
    Route::get('/blocked-emails', [BlockedEmailController::class, 'index'])->name('blocked-emails.index');
    Route::post('/blocked-emails', [BlockedEmailController::class, 'store'])->name('blocked-emails.store');
    Route::delete('/blocked-emails/{blockedEmail}', [BlockedEmailController::class, 'destroy'])->name('blocked-emails.delete');

    // Support tickets management
    Route::get('/tickets', [AdminSupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminSupportTicketController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}/status', [AdminSupportTicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::delete('/tickets/{ticket}', [AdminSupportTicketController::class, 'destroy'])->name('tickets.delete');
    Route::post('/tickets/{ticket}/disable-2fa', [AdminSupportTicketController::class, 'disableTwoFactor'])->name('tickets.disableTwoFactor');
});

require __DIR__.'/auth.php';
