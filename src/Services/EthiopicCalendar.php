<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Services;

use InvalidArgumentException;

final class EthiopicCalendar
{
    public const DATE_FORMAT = '%04d-%02d-%02d';

    private const ETHIOPIC_EPOCH_JDN = 1724221;

    /**
     * @return array{year:int,month:int,day:int}
     */
    public function toEthiopic(int $year, int $month, int $day): array
    {
        $this->assertValidGregorianDate($year, $month, $day);

        $jdn = $this->gregorianToJdn($year, $month, $day);

        return $this->jdnToEthiopic($jdn);
    }

    /**
     * @return array{year:int,month:int,day:int}
     */
    public function toGregorian(int $year, int $month, int $day): array
    {
        $this->assertValidEthiopicDate($year, $month, $day);

        $jdn = $this->ethiopicToJdn($year, $month, $day);

        return $this->jdnToGregorian($jdn);
    }

    public function toEthiopicString(?string $gregorian): ?string
    {
        if ($gregorian === null || trim($gregorian) === '') {
            return null;
        }

        $parsed = $this->parseDateString($gregorian);

        if ($parsed === null) {
            return null;
        }

        [$year, $month, $day] = $parsed;

        if (! checkdate($month, $day, $year)) {
            return null;
        }

        $ethiopic = $this->toEthiopic($year, $month, $day);

        return $this->formatDate($ethiopic['year'], $ethiopic['month'], $ethiopic['day']);
    }

    public function toGregorianString(?string $ethiopic): ?string
    {
        if ($ethiopic === null || trim($ethiopic) === '') {
            return null;
        }

        $parsed = $this->parseDateString($ethiopic);

        if ($parsed === null) {
            return null;
        }

        [$year, $month, $day] = $parsed;

        if (! $this->isValidEthiopicDate($year, $month, $day)) {
            return null;
        }

        $gregorian = $this->toGregorian($year, $month, $day);

        return $this->formatDate($gregorian['year'], $gregorian['month'], $gregorian['day']);
    }

    public function isValidEthiopicDateString(?string $ethiopic): bool
    {
        return $this->toGregorianString($ethiopic) !== null;
    }

    public function daysInEthiopicMonth(int $year, int $month): int
    {
        if ($month < 1 || $month > 13) {
            throw new InvalidArgumentException('Invalid Ethiopic month provided.');
        }

        if ($month <= 12) {
            return 30;
        }

        return $this->isLeapYear($year) ? 6 : 5;
    }

    private function formatDate(int $year, int $month, int $day): string
    {
        return sprintf(self::DATE_FORMAT, $year, $month, $day);
    }

    private function gregorianToJdn(int $year, int $month, int $day): int
    {
        $a = intdiv(14 - $month, 12);
        $y = $year + 4800 - $a;
        $m = $month + (12 * $a) - 3;

        return $day
            + intdiv(153 * $m + 2, 5)
            + (365 * $y)
            + intdiv($y, 4)
            - intdiv($y, 100)
            + intdiv($y, 400)
            - 32045;
    }

    /**
     * @return array{year:int,month:int,day:int}
     */
    private function jdnToGregorian(int $jdn): array
    {
        $a = $jdn + 32044;
        $b = intdiv(4 * $a + 3, 146097);
        $c = $a - intdiv(146097 * $b, 4);

        $d = intdiv(4 * $c + 3, 1461);
        $e = $c - intdiv(1461 * $d, 4);
        $m = intdiv(5 * $e + 2, 153);

        $day = $e - intdiv(153 * $m + 2, 5) + 1;
        $month = $m + 3 - 12 * intdiv($m, 10);
        $year = 100 * $b + $d - 4800 + intdiv($m, 10);

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }

    private function ethiopicToJdn(int $year, int $month, int $day): int
    {
        return self::ETHIOPIC_EPOCH_JDN
            + (365 * ($year - 1))
            + intdiv($year, 4)
            + (30 * $month)
            + $day
            - 31;
    }

    /**
     * @return array{year:int,month:int,day:int}
     */
    private function jdnToEthiopic(int $jdn): array
    {
        $year = intdiv((4 * ($jdn - self::ETHIOPIC_EPOCH_JDN)) + 1463, 1461);
        $month = intdiv($jdn - $this->ethiopicToJdn($year, 1, 1), 30) + 1;
        $day = $jdn - $this->ethiopicToJdn($year, $month, 1) + 1;

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }

    private function isValidEthiopicDate(int $year, int $month, int $day): bool
    {
        if ($year < 1 || $month < 1 || $month > 13 || $day < 1) {
            return false;
        }

        return $day <= $this->daysInEthiopicMonth($year, $month);
    }

    private function isLeapYear(int $year): bool
    {
        return ($year + 1) % 4 === 0;
    }

    private function assertValidGregorianDate(int $year, int $month, int $day): void
    {
        if (! checkdate($month, $day, $year)) {
            throw new InvalidArgumentException('Invalid Gregorian date provided.');
        }
    }

    private function assertValidEthiopicDate(int $year, int $month, int $day): void
    {
        if (! $this->isValidEthiopicDate($year, $month, $day)) {
            throw new InvalidArgumentException('Invalid Ethiopic date provided.');
        }
    }

    /**
     * @return array{0:int,1:int,2:int}|null
     */
    private function parseDateString(string $date): ?array
    {
        if (! preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            return null;
        }

        return [
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3],
        ];
    }
}
