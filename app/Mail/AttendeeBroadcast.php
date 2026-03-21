<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendeeBroadcast extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $event;
    public $subjectLine;
    public $broadcastMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, $subjectLine, $broadcastMessage)
    {
        $this->event = $event;
        $this->subjectLine = $subjectLine;
        $this->broadcastMessage = $broadcastMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->event->user->email],
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attendee_broadcast',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
