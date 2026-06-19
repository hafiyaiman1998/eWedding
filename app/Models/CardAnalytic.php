<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CardAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_card_id',
        'event_type',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the wedding card that this analytic belongs to.
     */
    public function weddingCard()
    {
        return $this->belongsTo(WeddingCard::class);
    }

    /**
     * Scope to get only view events.
     */
    public function scopeViews($query)
    {
        return $query->where('event_type', 'view');
    }

    /**
     * Scope to get only RSVP events.
     */
    public function scopeRsvps($query)
    {
        return $query->whereIn('event_type', ['rsvp_yes', 'rsvp_no']);
    }

    /**
     * Scope to get only positive RSVP events.
     */
    public function scopeRsvpYes($query)
    {
        return $query->where('event_type', 'rsvp_yes');
    }

    /**
     * Scope to get only negative RSVP events.
     */
    public function scopeRsvpNo($query)
    {
        return $query->where('event_type', 'rsvp_no');
    }

    /**
     * Scope to get only share events.
     */
    public function scopeShares($query)
    {
        return $query->where('event_type', 'share');
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Static method to track an event.
     */
    public static function track($weddingCardId, $eventType, $metadata = [])
    {
        return static::create([
            'wedding_card_id' => $weddingCardId,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'metadata' => $metadata,
        ]);
    }
} 