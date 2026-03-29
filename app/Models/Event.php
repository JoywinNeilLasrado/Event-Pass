<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use SoftDeletes;
    
    protected $appends = ['remaining', 'poster_image_url'];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'date',
        'time',
        'location',
        'available_tickets',
        'poster_image',
        'images',
        'is_featured',
        'is_published',
        'payment_status',
        'cashfree_order_id',
        'payout_status',
        'payout_amount',
        'payout_reference_id',
    ];

    protected $casts = [
        'date' => 'date',
        'images' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }

    public function getRemainingAttribute()
    {
        return $this->available_tickets - $this->bookings()->count();
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    public function getPosterImageUrlAttribute()
    {
        if (!$this->poster_image) {
            return 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80&w=800';
        }
        return url(Storage::url($this->poster_image));
    }
}
