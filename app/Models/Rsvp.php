<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'number_of_guests' => 'integer',
        ];
    }

    /**
     * Get the wedding card that this RSVP belongs to.
     */
    public function weddingCard(): BelongsTo
    {
        return $this->belongsTo(WeddingCard::class);
    }

    /**
     * Scope to get only attending guests.
     */
    public function scopeAttending(Builder $query): Builder
    {
        return $query->where('attendance_status', AttendanceStatus::Yes->value);
    }

    /**
     * Scope to get only non-attending guests.
     */
    public function scopeNotAttending(Builder $query): Builder
    {
        return $query->where('attendance_status', AttendanceStatus::No->value);
    }

    /**
     * Get the total number of guests for this RSVP.
     */
    protected function totalGuests(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->attendance_status === AttendanceStatus::Yes->value
                ? $this->number_of_guests
                : 0,
        );
    }

    /**
     * Get formatted attendance status.
     */
    protected function formattedAttendance(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->attendance_status === AttendanceStatus::Yes->value
                ? AttendanceStatus::Yes->label()
                : AttendanceStatus::No->label(),
        );
    }
}
