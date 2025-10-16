<?php

namespace ganzobenlinks\karneval\Utils;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class Datefunctions
{
    public static function isLeapYear(int $year): bool
    {
        return (bool) date('L', mktime(0, 0, 0, 1, 1, $year));
    }

    public static function dateIsBetween(DateTime $date, DateTime $start, DateTime $end, bool $inclusive = true): bool
    {
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        if ($inclusive) {
            return ($date >= $start && $date <= $end);
        }
        return ($date > $start && $date < $end);
    }
    public static function getEasterSunday(int $year, ?DateTimeZone $tz = null): DateTimeImmutable
    {
        // Meeus/Jones/Butcher algorithm (Gregorian calendar) — consistent across all PHP builds/timezones
        [$y, $a, $b, $c] = [$year, $year % 19, intdiv($year, 100), $year % 100];
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31); // 3=March, 4=April
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $y, $month, $day), $tz);
    }
}