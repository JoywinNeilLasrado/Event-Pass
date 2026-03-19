<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/scan', function () {
    return view('scan');
})->middleware('auth')->name('scan');

// --- Event Routes ---
// IMPORTANT: 'create' must come BEFORE '{event}' to avoid route conflict
Route::get('/events', [EventController::class, 'index'])->name('events.index');

// Auth-only: create form (must be above show/{event})
Route::middleware('auth')->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});

// Public: show a single event (wildcard comes AFTER /create)
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Auth + ownership: edit, update, delete, manage attendees
Route::middleware(['auth', 'event.owner'])->group(function () {
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
});

// --- Profile Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('/events/{event}/book', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::get('/events/{event}/ticket', [BookingController::class, 'downloadTicket'])->name('bookings.ticket');
});

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
