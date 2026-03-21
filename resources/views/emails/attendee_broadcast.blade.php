<!DOCTYPE html>
<html>
<head>
    <title>Message regarding {{ $event->title }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #111;">Update from the Organizer of {{ $event->title }}</h2>
        
        <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb;">
            {!! nl2br(e($broadcastMessage)) !!}
        </div>
        
        <p style="color: #666; font-size: 14px;">
            This message was sent by the organizer of <strong>{{ $event->title }}</strong>.<br>
            If you have any questions, you can reply directly to this email to reach them at {{ $event->user->email }}.
        </p>
    </div>
</body>
</html>
