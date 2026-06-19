<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_card_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'attendance_status',
        'number_of_guests',
        'message',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'number_of_guests' => 'integer',
    ];

    /**
     * Get the wedding card that this RSVP belongs to.
     */
    public function weddingCard()
    {
        return $this->belongsTo(WeddingCard::class);
    }

    /**
     * Scope to get only attending guests.
     */
    public function scopeAttending($query)
    {
        return $query->where('attendance_status', 'yes');
    }

    /**
     * Scope to get only non-attending guests.
     */
    public function scopeNotAttending($query)
    {
        return $query->where('attendance_status', 'no');
    }

    /**
     * Get the total number of guests for this RSVP.
     */
    public function getTotalGuestsAttribute()
    {
        return $this->attendance_status === 'yes' ? $this->number_of_guests : 0;
    }

    /**
     * Get formatted attendance status.
     */
    public function getFormattedAttendanceAttribute()
    {
        return $this->attendance_status === 'yes' ? 'Attending' : 'Not Attending';
    }
} 