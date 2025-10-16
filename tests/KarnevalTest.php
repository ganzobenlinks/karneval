<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ganzobenlinks\karneval\Karneval;

final class KarnevalTest extends TestCase
{
    private function easterSunday(int $year): DateTimeImmutable
    {
        // Meeus/Jones/Butcher algorithm (Gregorian)
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day), new DateTimeZone('Europe/Berlin'));
    }

    private function expectedRangeStrings(int $year): array
    {
        $easter = $this->easterSunday($year);
        $weiber = $easter->sub(new DateInterval('P' . abs(-52) . 'D'));
        $aschermittwoch = $easter->sub(new DateInterval('P' . abs(-46) . 'D'));
        $rosenmontag = $easter->sub(new DateInterval('P' . abs(-48) . 'D'));
        return [
            'range' => $weiber->format('d.m.Y') . ' - ' . $aschermittwoch->format('d.m.Y'),
            'rosenmontag' => $rosenmontag->format('d.m.Y'),
        ];
    }

    public function testNonLeapYear2025(): void
    {
        $k = new Karneval(2025);
        $expected = $this->expectedRangeStrings(2025);
        $this->assertSame('03.03.2025', $expected['rosenmontag']); // sanity check of helper
        $this->assertSame($expected['rosenmontag'], $k->rosenmontag());
        $this->assertSame($expected['range'], $k->asString());
    }

    public function testLeapYear2024(): void
    {
        $k = new Karneval(2024);
        $expected = $this->expectedRangeStrings(2024);
        $this->assertSame('12.02.2024', $expected['rosenmontag']); // sanity check
        $this->assertSame($expected['rosenmontag'], $k->rosenmontag());
        $this->assertSame($expected['range'], $k->asString());
    }

    public function testPre1970Year1960(): void
    {
        $k = new Karneval(1960);
        $expected = $this->expectedRangeStrings(1960);
        // Known special case: Rosenmontag 1960 falls on leap day 29.02.1960
        $this->assertSame('29.02.1960', $expected['rosenmontag']);
        $this->assertSame($expected['rosenmontag'], $k->rosenmontag());
        $this->assertSame($expected['range'], $k->asString());
    }

    public function testYear2026(): void
    {
        $k = new Karneval(2026);
        $expected = $this->expectedRangeStrings(2026);
        $this->assertSame($expected['rosenmontag'], $k->rosenmontag());
        $this->assertSame($expected['range'], $k->asString());
    }
}
