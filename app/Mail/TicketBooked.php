<?php

namespace App\Mail;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketBooked extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ticket for ' . $this->booking->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-booked',
        );
    }

    public function attachments(): array
    {
        $event = $this->booking->event;
        $booking = $this->booking;

        $verifyUrl = URL::signedRoute('tickets.verify', ['booking' => $booking->id]);
        $svgData = file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=svg&data=' . urlencode($verifyUrl));
        $booking->qrCode = base64_encode($svgData);

        $bookings = [$booking];
        $pdf = Pdf::loadView('bookings.ticket', compact('bookings', 'event'));

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Passage-Ticket-' . $event->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }

}
