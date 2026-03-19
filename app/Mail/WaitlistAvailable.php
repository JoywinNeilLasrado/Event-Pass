<?php

namespace App\Mail;

use App\Models\Waitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaitlistAvailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Waitlist $waitlist)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Good News! A ticket opened up for ' . $this->waitlist->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.waitlist-available',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
