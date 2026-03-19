<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'price',
        'capacity',
        'description',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getRemainingAttribute()
    {
        return $this->capacity - $this->bookings()->count();
    }

    public function waitlists()
    {
        return $this->hasMany(Waitlist::class);
    }
}
