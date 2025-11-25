<?php

namespace Source\Utils;

use DateTime;

class DateBr
{
    public static function convertToDateTime(string $date, ?string $time = null): ?DateTime
    {
        error_log("DateBr::convertToDateTime - Data recebida: '{$date}', Hora: '{$time}'");

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            error_log("Formato ISO detectado");
            $dateIso = $date;
        }

        elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            error_log("Formato brasileiro detectado");
            $parts = explode('/', $date);
            if (count($parts) === 3) {
                $dateIso = "$parts[2]-$parts[1]-$parts[0]";
            } else {
                error_log("ERRO: Não foi possível dividir a data em partes");
                return null;
            }
        } else {
            error_log("ERRO: Formato de data não reconhecido: {$date}");
            return null;
        }

        try {
            $dateTimeString = $time ? "$dateIso $time" : $dateIso;
            error_log("String DateTime final: {$dateTimeString}");

            $dateTime = new DateTime($dateTimeString);
            error_log("DateTime criado com sucesso: " . $dateTime->format('Y-m-d H:i:s'));

            return $dateTime;
        } catch (\Exception $e) {
            error_log("ERRO ao criar DateTime: " . $e->getMessage());
            return null;
        }
    }

    public static function formatDateTime(DateTime $dateTime): array
    {
        return [
            'date' => $dateTime->format('d/m/Y'),
            'time' => $dateTime->format('H:i')
        ];
    }
}