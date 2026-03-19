<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Models\Event;
use App\Mail\EventReminder;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:send-reminders', function () {
    $this->info('Scanning database for upcoming events...');
    $tomorrow = now()->addDay()->toDateString();
    
    $events = Event::with('bookings.user')->whereDate('date', $tomorrow)->get();
    
    $emailsSent = 0;
    foreach ($events as $event) {
        foreach ($event->bookings as $booking) {
            if ($booking->user) {
                Mail::to($booking->user->email)->send(new EventReminder($event, $booking));
                $emailsSent++;
            }
        }
    }
    
    $this->info("Job Complete: Sent {$emailsSent} reminder emails to attendees!");
})->purpose('Dispatch exactly-24-hour reminder emails to attendees');

// The magical Cron Job scheduling line
Schedule::command('app:send-reminders')->dailyAt('08:00');
