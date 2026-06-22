<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Yes = 'yes';
    case No = 'no';

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
            self::Yes => 'Attending',
            self::No => 'Not Attending',
        };
    }
}
