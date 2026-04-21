<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Contracts;

interface CalendarAdapterInterface
{
    /**
     * Convert a Gregorian date to Ethiopic.
     *
     * @return array{year: int, month: int, day: int}
     */
    public function toEthiopic(int $year, int $month, int $day): array;

    /**
     * Convert an Ethiopic date to Gregorian.
     *
     * @return array{year: int, month: int, day: int}
     */
    public function toGregorian(int $year, int $month, int $day): array;

    /**
     * Convert a Gregorian date string (Y-m-d) to an Ethiopic date string.
     */
    public function toEthiopicString(?string $gregorian): ?string;

    /**
     * Convert an Ethiopic date string (Y-m-d) to a Gregorian date string.
     */
    public function toGregorianString(?string $ethiopic): ?string;

    /**
     * Get the number of days in a given Ethiopic month.
     */
    public function daysInEthiopicMonth(int $year, int $month): int;

    /**
     * Validate whether a string represents a valid Ethiopic date.
     */
    public function isValidEthiopicDateString(?string $ethiopic): bool;
}
