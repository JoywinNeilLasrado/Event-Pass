<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'bio', 'profile_picture', 'is_admin', 'is_organizer', 'has_unlimited_events', 'cashfree_vendor_id', 'employer_id', 'kyc_status', 'business_details', 'social_links'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user is an organizer.
     * Admins are automatically considered organizers.
     *
     * @param  bool  $value
     * @return bool
     */
    public function getIsOrganizerAttribute($value)
    {
        return $this->is_admin || (bool) $value;
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'employer_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
