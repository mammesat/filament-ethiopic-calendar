<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Services;

use Mammesat\FilamentEthiopicDatePicker\Enums\DisplayMode;

final class EthiopicCalendar
{
    public const DATE_FORMAT = '%04d-%02d-%02d';

    private const ETHIOPIC_EPOCH_JDN = 1724221;

    private const AMHARIC_MONTHS = [
        1 => 'መስከረም', 2 => 'ጥቅምት', 3 => 'ኅዳር', 4 => 'ታኅሣሥ',
        5 => 'ጥር', 6 => 'የካቲት', 7 => 'መጋቢት', 8 => 'ሚያዝያ',
        9 => 'ግንቦት', 10 => 'ሰኔ', 11 => 'ሐምሌ', 12 => 'ነሐሴ', 13 => 'ጳጉሜ',
    ];

    private const ENGLISH_MONTHS = [
        1 => 'Meskerem', 2 => 'Tikimt', 3 => 'Hidar', 4 => 'Tahsas',
        5 => 'Tir', 6 => 'Yekatit', 7 => 'Megabit', 8 => 'Miyazya',
        9 => 'Ginbot', 10 => 'Sene', 11 => 'Hamle', 12 => 'Nehase', 13 => 'Pagume',
    ];

    private const AMHARIC_DAYS = [
        1 => 'ሰኞ', 2 => 'ማክሰኞ', 3 => 'ረቡዕ', 4 => 'ሐሙስ', 5 => 'አርብ', 6 => 'ቅዳሜ', 7 => 'እሁድ',
    ];

    // Follows ISO-8601 (1 = Monday, 7 = Sunday)
    private const ENGLISH_DAYS = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];

    public function getDisplayMonthName(int $monthIndex, DisplayMode|string|null $mode = null): string
    {
        $mode = $this->resolveMode($mode);

        return match ($mode) {
            DisplayMode::TransliterationCombined, DisplayMode::TransliterationNoWeek, DisplayMode::CleanGregorian => self::ENGLISH_MONTHS[$monthIndex] ?? (string) $monthIndex,
            DisplayMode::Hybrid => (self::ENGLISH_MONTHS[$monthIndex] ?? '') . ' (' . (self::AMHARIC_MONTHS[$monthIndex] ?? '') . ')',
            DisplayMode::CompactAmharic, DisplayMode::AmharicCombined, DisplayMode::AmharicNoWeek => self::AMHARIC_MONTHS[$monthIndex] ?? (string) $monthIndex,
        };
    }

    public function getDisplayDayName(int $dayIndex, DisplayMode|string|null $mode = null): string
    {
        $mode = $this->resolveMode($mode);
        $index = $dayIndex === 0 ? 7 : $dayIndex; // Carbon ->dayOfWeek returns 0 for Sunday

        return match ($mode) {
            DisplayMode::AmharicNoWeek, DisplayMode::TransliterationNoWeek => '',
            DisplayMode::TransliterationCombined, DisplayMode::CleanGregorian => self::ENGLISH_DAYS[$index] ?? (string) $index,
            DisplayMode::Hybrid => (self::ENGLISH_DAYS[$index] ?? '') . ' (' . (self::AMHARIC_DAYS[$index] ?? '') . ')',
            DisplayMode::CompactAmharic, DisplayMode::AmharicCombined => self::AMHARIC_DAYS[$index] ?? (string) $index,
        };
    }

    public function formatDisplayLabel(?string $gregorian, DisplayMode|string|null $mode = null): ?string
    {
        if ($gregorian === null || trim($gregorian) === '') {
            return null;
        }

        $mode = $this->resolveMode($mode);

        if ($mode === DisplayMode::CleanGregorian) {
            return $gregorian;
        }

        $ethiopicString = $this->toEthiopicString($gregorian);
        
        if ($ethiopicString === null) {
            return null;
        }

        [$year, $month, $day] = explode('-', $ethiopicString);
        
        // Calculate the weekday. Ethiopic dates map 1:1 to specific Greg dates, so weekday is identical
        $dayOfWeek = \Carbon\Carbon::parse($gregorian)->dayOfWeekIso;

        $monthName = $this->getDisplayMonthName((int) $month, $mode);
        $dayName = $this->getDisplayDayName($dayOfWeek, $mode);

        return match ($mode) {
            DisplayMode::AmharicCombined, DisplayMode::TransliterationCombined, DisplayMode::Hybrid => "{$monthName} {$day}, {$year} / {$dayName}",
            DisplayMode::CompactAmharic => "{$monthName} {$day}, {$year} {$dayName}",
            DisplayMode::AmharicNoWeek, DisplayMode::TransliterationNoWeek => "{$monthName} {$day}, {$year}",
            DisplayMode::CleanGregorian => $gregorian,
        };
    }

    public function formatEthiopianTime(?string $time): ?string
    {
        if ($time === null) {
            return null;
        }

        $time = trim($time);

        if ($time === '') {
            return null;
        }

        if (strtotime($time) === false) {
            return null;
        }

        if (! preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            return null;
        }

        $gregorianHour = (int) $matches[1];
        $minute = (int) $matches[2];

        if ($gregorianHour < 0 || $gregorianHour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        $ethiopianHour = ($gregorianHour + 6) % 12;

        if ($ethiopianHour === 0) {
            $ethiopianHour = 12;
        }

        $period = match (true) {
            $gregorianHour >= 6 && $gregorianHour <= 11 => 'ጥዋት',
            $gregorianHour >= 12 && $gregorianHour <= 17 => 'ከሰዓት',
            $gregorianHour >= 18 && $gregorianHour <= 23 => 'ማታ',
            default => 'ለሊት',
        };

        return sprintf('%s %d:%s', $period, $ethiopianHour, sprintf('%02d', $minute));
    }

    // ──────────────────────────────────────────────
    // Static convenience methods for table/infolist usage
    // ──────────────────────────────────────────────

    /**
     * Static shorthand for formatDisplayLabel().
     *
     * Usage in Filament tables/infolists:
     *   TextColumn::make('birth_date')
     *       ->formatStateUsing(fn ($state) => EthiopicCalendar::formatEthiopicDisplay($state));
     */
    public static function formatEthiopicDisplay(?string $gregorianDate, DisplayMode|string|null $mode = null): ?string
    {
        return app(static::class)->formatDisplayLabel($gregorianDate, $mode);
    }

    /**
     * Static shorthand for getDisplayMonthName().
     */
    public static function getMonthName(int $month, DisplayMode|string|null $mode = null): string
    {
        return app(static::class)->getDisplayMonthName($month, $mode);
    }

    /**
     * Static shorthand for getDisplayDayName().
     */
    public static function getDayName(int $dayIndex, DisplayMode|string|null $mode = null): string
    {
        return app(static::class)->getDisplayDayName($dayIndex, $mode);
    }

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

    private function resolveMode(DisplayMode|string|null $mode): DisplayMode
    {
        if ($mode instanceof DisplayMode) {
            return $mode;
        }

        if (is_string($mode)) {
            return DisplayMode::tryFrom($mode) ?? DisplayMode::fromConfig();
        }

        return DisplayMode::fromConfig();
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
