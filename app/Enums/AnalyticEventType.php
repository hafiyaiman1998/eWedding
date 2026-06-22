<?php

namespace App\Enums;

enum AnalyticEventType: string
{
    case View = 'view';
    case Share = 'share';
    case RsvpYes = 'rsvp_yes';
    case RsvpNo = 'rsvp_no';

    /**
     * Get all enum values.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::View => 'View',
            self::Share => 'Share',
            self::RsvpYes => 'RSVP Yes',
            self::RsvpNo => 'RSVP No',
        };
    }
}
