<?php

namespace App\Models;

use App\Enums\AnalyticEventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * Get the wedding card that this analytic belongs to.
     */
    public function weddingCard(): BelongsTo
    {
        return $this->belongsTo(WeddingCard::class);
    }

    /**
     * Scope to get only view events.
     */
    public function scopeViews(Builder $query): Builder
    {
        return $query->where('event_type', AnalyticEventType::View->value);
    }

    /**
     * Scope to get only RSVP events.
     */
    public function scopeRsvps(Builder $query): Builder
    {
        return $query->whereIn('event_type', [
            AnalyticEventType::RsvpYes->value,
            AnalyticEventType::RsvpNo->value,
        ]);
    }

    /**
     * Scope to get only positive RSVP events.
     */
    public function scopeRsvpYes(Builder $query): Builder
    {
        return $query->where('event_type', AnalyticEventType::RsvpYes->value);
    }

    /**
     * Scope to get only negative RSVP events.
     */
    public function scopeRsvpNo(Builder $query): Builder
    {
        return $query->where('event_type', AnalyticEventType::RsvpNo->value);
    }

    /**
     * Scope to get only share events.
     */
    public function scopeShares(Builder $query): Builder
    {
        return $query->where('event_type', AnalyticEventType::Share->value);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange(Builder $query, mixed $startDate, mixed $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Static method to track an event.
     */
    public static function track(int $weddingCardId, string|AnalyticEventType $eventType, array $metadata = []): self
    {
        return static::create([
            'wedding_card_id' => $weddingCardId,
            'event_type' => $eventType instanceof AnalyticEventType ? $eventType->value : $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'metadata' => $metadata,
        ]);
    }
}
