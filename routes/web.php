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
use App\Http\Controllers\CashfreeController;
use Illuminate\Support\Facades\Route;

Route::post('/cashfree/webhook', [PaymentController::class, 'webhook'])->name('cashfree.webhook');

Route::get('/', function () {
    $featuredEvents = \App\Models\Event::where('is_featured', true)
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date', 'asc')
        ->with('category', 'tags')
        ->take(5)
        ->get();
        
    $categories = \App\Models\Category::all();
        
    return view('welcome', compact('featuredEvents', 'categories'));
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'organizer'])
    ->name('dashboard');

Route::get('/scan', function () {
    if (!auth()->user()->is_organizer && !auth()->user()->employer_id) {
        abort(403, 'Access Denied: Only Organizers and their verified Staff can access the Scanner module.');
    }
    return view('scan');
})->middleware(['auth'])->name('scan');

// --- Team / Staff Management ---
Route::middleware(['auth', 'organizer'])->group(function () {
    Route::post('/team/staff', [\App\Http\Controllers\TeamController::class, 'store'])->name('team.store');
    Route::delete('/team/staff/{staff}', [\App\Http\Controllers\TeamController::class, 'destroy'])->name('team.destroy');
});

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
Route::post('/events/{event}/promo-codes/validate', [PromoCodeController::class, 'validateCode'])->name('promo_codes.validate')->middleware('throttle:10,1');

// Auth + ownership: edit, update, delete, manage attendees
Route::middleware(['auth', 'event.owner'])->group(function () {
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::post('/events/{event}/message-attendees', [EventController::class, 'messageAttendees'])->name('events.message_attendees');
    Route::get('/events/{event}/export', [EventController::class, 'exportAttendees'])->name('events.export');
    Route::get('/events/{event}/pay', [EventController::class, 'retryPublishPayment'])->name('events.retry_publish_payment');
    
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
    Route::post('/upgrade/basic', [UpgradeController::class, 'checkoutBasic'])->name('upgrade.basic')->middleware('throttle:5,1');
    Route::post('/upgrade/pro', [UpgradeController::class, 'checkoutPro'])->name('upgrade.pro')->middleware('throttle:5,1');
    Route::post('/upgrade/cancel', [UpgradeController::class, 'cancel'])->name('upgrade.cancel');

    // Bookings
    Route::get('/my-tickets', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store')->middleware('throttle:10,1');
    Route::delete('/events/{event}/cancel', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/events/{event}/waitlist', [WaitlistController::class, 'store'])->name('waitlists.store')->middleware('throttle:10,1');
    Route::get('/events/{event}/ticket', [BookingController::class, 'downloadTicket'])->name('bookings.ticket');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Organizers
Route::get('/organizers/{user}', [OrganizerController::class, 'show'])->name('organizers.show');

// Cashfree Connect
Route::middleware(['auth', 'organizer'])->group(function () {
    Route::post('/organizer/cashfree/connect', [CashfreeController::class, 'connect'])->name('cashfree.connect');
    Route::delete('/organizer/cashfree/disconnect', [CashfreeController::class, 'disconnect'])->name('cashfree.disconnect');
});

// Ticket Verification (Public but protected by Signed URL)
Route::get('/tickets/verify/{booking}', [BookingController::class, 'verifyTicket'])->name('tickets.verify')->middleware('signed');
Route::post('/tickets/checkin/{booking}', [BookingController::class, 'checkInTicket'])->name('tickets.checkin')->middleware('auth');

// --- API Resource Route ---
Route::middleware('auth')->get('/api/events', function () {
    $events = Event::with(['category', 'user', 'tags'])->paginate(15);
    return EventResource::collection($events);
})->name('api.events');

// --- Developer Mail Preview Route ---
if (app()->environment('local')) {
    Route::get('/preview-reminder', function () {
        $event = Event::first();
        $booking = App\Models\Booking::first();
        return new App\Mail\EventReminder($event, $booking);
    });
}

// --- KYC Verification Flow ---
Route::middleware(['auth'])->group(function () {
    Route::get('/kyc/setup', [\App\Http\Controllers\KycController::class, 'setup'])->name('kyc.setup');
    Route::post('/kyc/submit', [\App\Http\Controllers\KycController::class, 'submit'])->name('kyc.submit');
});

require __DIR__.'/auth.php';
