<?php

namespace Source\Utils;

use DateTime;

class DateBr
{
    public static function convertToDateTime(string $date, ?string $time = null): ?DateTime
    {
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            $dateIso = "$parts[2]-$parts[1]-$parts[0]";
            return new DateTime($time ? "$dateIso $time" : $dateIso);
        }
        return null;
    }

    public static function formatDateTime(DateTime $dateTime): array
    {
        return [
            'date' => $dateTime->format('d/m/Y'),
            'time' => $dateTime->format('H:i')
        ];
    }
}
