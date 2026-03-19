<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_type_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
}
