<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f9; color: #111827; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f7f7f9; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04); border: 1px solid #f3f4f6; }
        .header { background: #000000; padding: 40px 30px; text-align: center; }
        .header .badge { display: inline-block; background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; border: 1px solid rgba(59, 130, 246, 0.3); }
        .header h1 { margin: 0; color: #ffffff; font-size: 28px; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2; }
        .accent { color: #a78bfa; }
        .content { padding: 40px 30px; }
        .content p { margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #4b5563; }
        .details-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin: 30px 0; }
        .details-box h3 { margin: 0 0 16px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; }
        .details-box ul { list-style: none; padding: 0; margin: 0; }
        .details-box li { margin-bottom: 12px; font-size: 16px; color: #111827; }
        .details-box li:last-child { margin-bottom: 0; }
        .details-box strong { color: #6b7280; width: 100px; display: inline-block; font-weight: 500; }
        .button-container { text-align: center; margin: 40px 0 10px; }
        .button { display: inline-block; background: #000000; color: #ffffff !important; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.15); }
        .footer { text-align: center; padding: 30px 20px; font-size: 13px; color: #9ca3af; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="badge">Event Tomorrow</div>
                <h1>Ready for <br><span class="accent">{{ $event->title }}</span>?</h1>
            </div>
            <div class="content">
                <p>Hi <strong>{{ explode(' ', $booking->user->name)[0] }}</strong>,</p>
                <p>Just a quick reminder that your event is happening exactly tomorrow! Gather your things and get ready for an amazing experience.</p>
                
                <div class="details-box">
                    <h3>Event Details</h3>
                    <ul>
                        <li><strong>Date:</strong> {{ $event->date->format('l, F j, Y') }}</li>
                        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</li>
                        <li><strong>Location:</strong> <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($event->location) }}" style="color: #4f46e5; text-decoration: none;" target="_blank">{{ $event->location }}</a></li>
                        <li style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed #d1d5db;">
                            <strong>Organizer:</strong> 
                            <div style="display:inline-block; vertical-align: top;">
                                {{ $event->user->name }}<br>
                                <a href="mailto:{{ $event->user->email }}" style="color: #4f46e5; text-decoration: none; font-size: 14px;">{{ $event->user->email }}</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <p>You can view or download your ticket anytime using the button below. Make sure to have your QR code ready for check-in.</p>

                <div class="button-container">
                    <a href="{{ route('bookings.ticket', $event->id) }}" class="button">View My Ticket</a>
                </div>
            </div>
            <div class="footer">
                Created by Joywin Neil Lasrado with ❤️<br><br>
                See you there!<br>
                The {{ config('app.name') }} Team
            </div>
        </div>
    </div>
</body>
</html>
