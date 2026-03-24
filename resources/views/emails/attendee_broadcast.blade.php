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
        .header h1 { margin: 0; color: #ffffff; font-size: 24px; font-weight: 800; letter-spacing: -0.025em; line-height: 1.3; }
        .header .subtitle { color: #9ca3af; font-size: 14px; font-weight: 500; margin-top: 8px; }
        .content { padding: 40px 30px; }
        .message-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin: 20px 0; font-size: 16px; line-height: 1.6; color: #111827; }
        .footer { text-align: center; padding: 30px 20px; font-size: 13px; color: #9ca3af; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>Update from the Organizer of {{ $event->title }}</h1>
                <div class="subtitle">Important message regarding your upcoming event</div>
            </div>
            <div class="content">
                <div class="message-box">
                    {!! nl2br(e($broadcastMessage)) !!}
                </div>
                
                <p style="color: #6b7280; font-size: 14px; text-align: center; margin-top: 30px;">
                    This message was sent by the organizer, <strong>{{ $event->user->name }}</strong>.<br>
                    You can reply directly to this email to reach them at <a href="mailto:{{ $event->user->email }}" style="color: #4f46e5;">{{ $event->user->email }}</a>.
                </p>
            </div>
            <div class="footer">
                Created by Joywin Neil Lasrado with ❤️
            </div>
        </div>
    </div>
</body>
</html>
