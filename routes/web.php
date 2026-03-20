<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\OrganizerController;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UpgradeController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

Route::get('/', function () {
    $featuredEvents = \App\Models\Event::where('is_featured', true)
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date', 'asc')
        ->with('category', 'tags')
        ->take(5)
        ->get();
        
    return view('welcome', compact('featuredEvents'));
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'organizer'])
    ->name('dashboard');

Route::get('/scan', function () {
    return view('scan');
})->middleware(['auth', 'organizer'])->name('scan');

// --- Event Routes ---
// IMPORTANT: 'create' must come BEFORE '{event}' to avoid route conflict
Route::get('/events', [EventController::class, 'index'])->name('events.index');

// Auth-only: create form (must be above show/{event})
Route::middleware(['auth', 'organizer'])->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});

// Public: show a single event (wildcard comes AFTER /create)
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/promo-codes/validate', [PromoCodeController::class, 'validateCode'])->name('promo_codes.validate');

// Auth + ownership: edit, update, delete, manage attendees
Route::middleware(['auth', 'event.owner'])->group(function () {
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::get('/events/{event}/export', [EventController::class, 'exportAttendees'])->name('events.export');
    
    // Promo Codes
    Route::get('/events/{event}/promo-codes', [PromoCodeController::class, 'index'])->name('promo_codes.index');
    Route::post('/events/{event}/promo-codes', [PromoCodeController::class, 'store'])->name('promo_codes.store');
    Route::delete('/events/{event}/promo-codes/{promoCode}', [PromoCodeController::class, 'destroy'])->name('promo_codes.destroy');
});

// --- Profile Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Upgrade
    Route::get('/upgrade', [UpgradeController::class, 'index'])->name('upgrade.index');
    Route::post('/upgrade/basic', [UpgradeController::class, 'checkoutBasic'])->name('upgrade.basic');
    Route::post('/upgrade/pro', [UpgradeController::class, 'checkoutPro'])->name('upgrade.pro');
    Route::post('/upgrade/cancel', [UpgradeController::class, 'cancel'])->name('upgrade.cancel');

    // Bookings
    Route::get('/my-tickets', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/events/{event}/cancel', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/events/{event}/waitlist', [WaitlistController::class, 'store'])->name('waitlists.store');
    Route::get('/events/{event}/ticket', [BookingController::class, 'downloadTicket'])->name('bookings.ticket');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Organizers
Route::get('/organizers/{user}', [OrganizerController::class, 'show'])->name('organizers.show');

// Ticket Verification (Public but protected by Signed URL)
Route::get('/tickets/verify/{booking}', [BookingController::class, 'verifyTicket'])->name('tickets.verify')->middleware('signed');
Route::post('/tickets/checkin/{booking}', [BookingController::class, 'checkInTicket'])->name('tickets.checkin')->middleware('auth');

// --- API Resource Route ---
Route::get('/api/events', function () {
    $events = Event::with(['category', 'user', 'tags'])->get();
    return EventResource::collection($events);
})->name('api.events');

// --- Developer Mail Preview Route ---
Route::get('/preview-reminder', function () {
    $event = Event::first();
    $booking = App\Models\Booking::first();
    return new App\Mail\EventReminder($event, $booking);
});

require __DIR__.'/auth.php';
