<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #111; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; border: 1px solid #eee; border-top: none; border-radius: 0 0 8px 8px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
        .button { display: inline-block; background: #111; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>You're going to {{ $booking->event->title }}!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $booking->user->name }},</p>
            <p>Your ticket for <strong>{{ $booking->event->title }}</strong> has been confirmed. We've attached your official PDF ticket to this email.</p>
            
            <h3>Event Details:</h3>
            <ul>
                <li><strong>Date:</strong> {{ $booking->event->date->format('l, F j, Y') }}</li>
                <li><strong>Time:</strong> {{ date('g:i A', strtotime($booking->event->time)) }}</li>
                <li><strong>Location:</strong> {{ $booking->event->location }}</li>
            </ul>

            <p>Please download the attached PDF and present the QR code at the venue for entry.</p>

            <div style="text-align: center;">
                <a href="{{ route('bookings.index') }}" class="button" style="color: white">View My Tickets</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} EventPass. All rights reserved.
        </div>
    </div>
</body>
</html>
