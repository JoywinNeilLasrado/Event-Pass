<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $event->title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 0; }
        .container { padding: 40px; }
        .ticket-wrapper { border: 2px dashed #999; border-radius: 10px; padding: 0; width: 100%; max-width: 700px; margin: 0 auto; overflow: hidden; page-break-inside: avoid; }
        .ticket-header { background-color: #111; color: #fff; padding: 25px; text-align: center; }
        .ticket-header h1 { margin: 0; font-size: 28px; letter-spacing: -0.5px; }
        .ticket-header p { margin: 5px 0 0 0; font-size: 14px; color: #ccc; }
        .ticket-body { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .label { font-size: 12px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .value { font-size: 18px; font-weight: bold; color: #111; margin-top: 0; margin-bottom: 20px; }
        .qr-wrapper { text-align: center; padding: 20px; background: #fafafa; border-radius: 8px; border: 1px solid #eee; }
        .qr-placeholder { font-size: 12px; color: #666; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-wrapper">
            <div class="ticket-header">
                <h1>{{ $event->title }}</h1>
                @if($booking->ticketType)
                    <p style="color: #ecc94b; font-weight: bold; font-size: 16px; letter-spacing: 2px;">{{ strtoupper($booking->ticketType->name) }} TICKET</p>
                @else
                    <p>EventPass Official Ticket</p>
                @endif
            </div>
            <div class="ticket-body">
                <table>
                    <tr>
                        <td style="width: 70%;">
                            @if($booking->ticketType)
                                <div class="label">Ticket Package</div>
                                <div class="value">{{ $booking->ticketType->name }}</div>
                            @endif

                            <div class="label">Ticket Holder</div>
                            <div class="value">{{ $booking->user->name }}</div>

                            <div class="label">Date & Time</div>
                            <div class="value">{{ $event->date->format('l, F j, Y') }} at {{ date('h:i A', strtotime($event->time)) }}</div>

                            <div class="label">Location</div>
                            <div class="value">{{ $event->location }}</div>

                            <div class="label">Booking ID</div>
                            <div class="value" style="font-family: monospace; font-size: 16px;">#EVT-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}-B{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</div>

                            <div class="label">Amount Paid</div>
                            <div class="value" style="font-size: 16px;">
                                @if($booking->amount_paid > 0)
                                    ${{ number_format($booking->amount_paid, 2) }}
                                @elseif($booking->ticketType && $booking->ticketType->price > 0 && $booking->amount_paid == 0)
                                    100% Promo Code OFF
                                @else
                                    FREE
                                @endif
                            </div>
                        </td>
                        <td style="width: 30%; text-align: right;">
                            <div class="qr-wrapper">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 250px; height: 250px; display: block; margin: 0 auto;">
                                <div class="qr-placeholder" style="margin-top: 15px;">Scan for entry</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="footer">
            Generated via EventPass on {{ now()->format('F j, Y, g:i a') }}<br>
            Please present this ticket at the venue.
        </div>
    </div>
</body>
</html>
