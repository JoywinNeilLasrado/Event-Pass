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
        .header .badge { display: inline-block; background: rgba(52, 211, 153, 0.2); color: #34d399; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px; border: 1px solid rgba(52, 211, 153, 0.3); }
        .header h1 { margin: 0; color: #ffffff; font-size: 28px; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2; }
        .accent { color: #a78bfa; }
        .content { padding: 40px 30px; }
        .content p { margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #4b5563; }
        .alert-box { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px; margin: 30px 0; color: #92400e; font-size: 15px; }
        .button-container { text-align: center; margin: 40px 0 10px; }
        .button { display: inline-block; background: #000000; color: #ffffff !important; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.15); }
        .footer { text-align: center; padding: 30px 20px; font-size: 13px; color: #9ca3af; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="badge">Waitlist Alert</div>
                <h1>A ticket is available for <br><span class="accent">{{ $waitlist->event->title }}</span>!</h1>
            </div>
            <div class="content">
                <p>Hi <strong>{{ explode(' ', $waitlist->user->name)[0] }}</strong>,</p>
                <p>Great news! A spot has opened up and a <strong>{{ $waitlist->ticketType->name }}</strong> ticket has just become available.</p>
                
                <div class="alert-box">
                    <strong>Act fast!</strong> Because you are on the waitlist, you have been notified first. However, tickets are released on a first-come, first-served basis.
                </div>
                
                <p style="font-size: 15px; color: #4b5563; background: #f9fafb; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <strong>Event Organizer:</strong> {{ $waitlist->event->user->name }}<br>
                    <a href="mailto:{{ $waitlist->event->user->email }}" style="color: #4f46e5; text-decoration: none; font-size: 14px;">{{ $waitlist->event->user->email }}</a>
                </p>

                <div class="button-container">
                    <a href="{{ route('events.show', $waitlist->event) }}" class="button">Claim Your Ticket Now</a>
                </div>
            </div>
            <div class="footer">
                Created by Joywin Neil Lasrado with ❤️<br><br>
                Don't want these emails? <a href="#" style="color: #6b7280; text-decoration: underline;">Unsubscribe</a>
            </div>
        </div>
    </div>
</body>
</html>
