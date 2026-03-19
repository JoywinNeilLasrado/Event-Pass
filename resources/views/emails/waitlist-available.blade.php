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
            <h1>A ticket is available!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $waitlist->user->name }},</p>
            <p>Great news! A {{ $waitlist->ticketType->name }} ticket has just become available for <strong>{{ $waitlist->event->title }}</strong>.</p>
            
            <p>Because you are on the waitlist, you have been notified. However, tickets are released on a first-come, first-served basis, so act fast!</p>

            <div style="text-align: center;">
                <a href="{{ route('events.show', $waitlist->event) }}" class="button" style="color: white">Book Ticket Now</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} EventPass. All rights reserved.
        </div>
    </div>
</body>
</html>
