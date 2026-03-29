<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScannerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\KycController;

/*
|--------------------------------------------------------------------------
| Mobile API Routes (Mirrored from Web)
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Public Featured Events (Mirror of GET /)
Route::get('/featured-events', function (Request $request) {
    $featuredEvents = \App\Models\Event::where('is_featured', true)
        ->where('is_published', true)
        ->where(function ($query) {
            $query->where('date', '>', now()->toDateString())
                  ->orWhere(function ($q) {
                      $q->where('date', '=', now()->toDateString())
                        ->where('time', '>=', now()->toTimeString());
                  });
        })
        ->orderBy('date', 'asc')
        ->with('category', 'tags')
        ->take(5)
        ->get();
        
    $categories = \App\Models\Category::all();
    return response()->json(compact('featuredEvents', 'categories'));
});

// Public Event Routes
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/organizers/{user}', [OrganizerController::class, 'show']);
Route::post('/events/{event}/promo-codes/validate', [PromoCodeController::class, 'validateCode'])->middleware('throttle:10,1');

// Protected Endpoints using JWT Tokens
Route::middleware('auth:api')->group(function () {
    // Auth Profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Scanner Endpoint
    Route::post('/scan', [ScannerController::class, 'scan']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('organizer');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Organizer / Team / Staff Management
    Route::middleware('organizer')->group(function () {
        Route::get('/team', [TeamController::class, 'index']);
        Route::post('/team/staff', [TeamController::class, 'store']);
        Route::delete('/team/staff/{staff}', [TeamController::class, 'destroy']);
        
        // Event Creation
        Route::post('/events', [EventController::class, 'store']);
        
        // Cashfree Connect
        Route::post('/organizer/cashfree/connect', [CashfreeController::class, 'connect']);
        Route::delete('/organizer/cashfree/disconnect', [CashfreeController::class, 'disconnect']);
    });

    // Event Ownership Routes
    Route::middleware('event.owner')->group(function () {
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        Route::get('/events/{event}/attendees', [EventController::class, 'attendees']);
        Route::post('/events/{event}/message-attendees', [EventController::class, 'messageAttendees']);
        Route::get('/events/{event}/export', [EventController::class, 'exportAttendees']);
        Route::get('/events/{event}/pay', [EventController::class, 'retryPublishPayment']);
        
        // Promo Codes
        Route::get('/events/{event}/promo-codes', [PromoCodeController::class, 'index']);
        Route::post('/events/{event}/promo-codes', [PromoCodeController::class, 'store']);
        Route::delete('/events/{event}/promo-codes/{promoCode}', [PromoCodeController::class, 'destroy']);
    });

    // Bookings & Attendee Actions
    Route::get('/my-tickets', [BookingController::class, 'index']);
    Route::get('/my-waitlist', [WaitlistController::class, 'index']);
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/bookings/{booking}/pay', [BookingController::class, 'payPending']);
    Route::delete('/events/{event}/cancel', [BookingController::class, 'destroy']);
    Route::post('/events/{event}/waitlist', [WaitlistController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/events/{event}/ticket', [BookingController::class, 'downloadTicket']);
    
    // Ticket Check-in explicitly for API
    Route::post('/tickets/checkin/{booking}', [BookingController::class, 'checkInTicket']);
    
    // Upgrades
    Route::get('/upgrade', [UpgradeController::class, 'index']);
    Route::post('/upgrade/basic', [UpgradeController::class, 'checkoutBasic'])->middleware('throttle:5,1');
    Route::post('/upgrade/pro', [UpgradeController::class, 'checkoutPro'])->middleware('throttle:5,1');
    Route::post('/upgrade/cancel', [UpgradeController::class, 'cancel']);

    // KYC
    Route::get('/kyc/setup', [KycController::class, 'setup']);
    Route::post('/kyc/submit', [KycController::class, 'submit']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
});
