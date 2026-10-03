<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Replica Carbon::diffForHumans() em pt_BR para paridade com mobile (lib/time.ts).
 * Strings idênticas: "agora mesmo", "há 1 minuto", "há X minutos", "há 1 hora",
 * "há X horas", "há 1 dia", "há X dias", "há 1 semana", "há X semanas",
 * "há 1 mês", "há X meses", "há 1 ano", "há X anos".
 */
class TimeHelper
{
    public static function timeAgo(string $dateString): string
    {
        try {
            $date = Carbon::parse($dateString);
        } catch (\Throwable) {
            return 'agora mesmo';
        }

        $now = Carbon::now();
        $diffInSeconds = $now->getTimestamp() - $date->getTimestamp();

        if ($diffInSeconds < 0) {
            $diffInSeconds = 0;
        }

        if ($diffInSeconds < 30) {
            return 'agora mesmo';
        }

        if ($diffInSeconds < 60) {
            return 'há 1 minuto';
        }

        $minutes = (int) ($diffInSeconds / 60);
        if ($minutes < 60) {
            return $minutes === 1 ? 'há 1 minuto' : "há {$minutes} minutos";
        }

        $hours = (int) ($minutes / 60);
        if ($hours < 24) {
            return $hours === 1 ? 'há 1 hora' : "há {$hours} horas";
        }

        $days = (int) ($hours / 24);
        if ($days < 7) {
            return $days === 1 ? 'há 1 dia' : "há {$days} dias";
        }

        $weeks = (int) ($days / 7);
        if ($weeks < 4) {
            return $weeks === 1 ? 'há 1 semana' : "há {$weeks} semanas";
        }

        $months = (int) ($days / 30);
        if ($months < 12) {
            return $months === 1 ? 'há 1 mês' : "há {$months} meses";
        }

        $years = (int) ($days / 365);

        return $years === 1 ? 'há 1 ano' : "há {$years} anos";
    }
}
