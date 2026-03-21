<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminTagController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingController; // Added this line
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{id}/restore', [AdminEventController::class, 'restore'])->name('events.restore');
    Route::delete('/events/{id}/force', [AdminEventController::class, 'forceDestroy'])->name('events.force-destroy');

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Tags
    Route::get('/tags', [AdminTagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [AdminTagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy'])->name('tags.destroy');

    // Bookings
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

    // KYC Approvals
    Route::get('/kyc', [\App\Http\Controllers\Admin\AdminKycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc/{user}/approve', [\App\Http\Controllers\Admin\AdminKycController::class, 'approve'])->name('kyc.approve');
    Route::post('/kyc/{user}/reject', [\App\Http\Controllers\Admin\AdminKycController::class, 'reject'])->name('kyc.reject');

    // Financials
    Route::get('/financials', [\App\Http\Controllers\Admin\AdminFinancialsController::class, 'index'])->name('financials.index');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Audit Logs
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AdminAuditLogController::class, 'index'])->name('audit-logs.index');

    // System Health & Queues
    Route::get('/system', [\App\Http\Controllers\Admin\AdminSystemHealthController::class, 'index'])->name('system.index');
    Route::post('/system/retry/{id}', [\App\Http\Controllers\Admin\AdminSystemHealthController::class, 'retry'])->name('system.retry');
    Route::post('/system/delete/{id}', [\App\Http\Controllers\Admin\AdminSystemHealthController::class, 'deleteFailed'])->name('system.delete');
    Route::post('/system/flush', [\App\Http\Controllers\Admin\AdminSystemHealthController::class, 'flushFailed'])->name('system.flush');
});
