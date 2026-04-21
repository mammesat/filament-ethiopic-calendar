<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit;

use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendarService;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicTimeService;
use Mammesat\FilamentEthiopicCalendar\Support\LocaleManager;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the central EthiopicFormatter.
 *
 * Uses mocked LocaleManager to avoid requiring a Laravel application context.
 */
final class EthiopicFormatterTest extends TestCase
{
    private EthiopicFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();

        $locale = $this->createStub(LocaleManager::class);
        $locale->method('getMonthName')->willReturnCallback(
            fn (int $index, ?string $loc = null) => match ($loc) {
                'en' => match ($index) {
                    1 => 'Meskerem', 2 => 'Tikimt', 3 => 'Hidar', 4 => 'Tahsas',
                    5 => 'Tir', 6 => 'Yekatit', 7 => 'Megabit', 8 => 'Miyazya',
                    9 => 'Ginbot', 10 => 'Sene', 11 => 'Hamle', 12 => 'Nehase', 13 => 'Pagume',
                    default => (string) $index,
                },
                default => match ($index) {
                    1 => 'መስከረም', 2 => 'ጥቅምት', 3 => 'ኅዳር', 4 => 'ታኅሣሥ',
                    5 => 'ጥር', 6 => 'የካቲት', 7 => 'መጋቢት', 8 => 'ሚያዝያ',
                    9 => 'ግንቦት', 10 => 'ሰኔ', 11 => 'ሐምሌ', 12 => 'ነሐሴ', 13 => 'ጳጉሜ',
                    default => (string) $index,
                },
            }
        );
        $locale->method('getDayName')->willReturnCallback(
            fn (int $index, string $length = 'long', ?string $loc = null) => match ($loc) {
                'en' => match ($index) {
                    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
                    5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
                    default => (string) $index,
                },
                default => match ($index) {
                    1 => 'ሰኞ', 2 => 'ማክሰኞ', 3 => 'ረቡዕ', 4 => 'ሐሙስ',
                    5 => 'አርብ', 6 => 'ቅዳሜ', 7 => 'እሁድ',
                    default => (string) $index,
                },
            }
        );
        $locale->method('getDayPeriod')->willReturnCallback(
            fn (string $key) => match ($key) {
                'morning' => 'ጠዋት', 'afternoon' => 'ከሰዓት',
                'evening' => 'ማታ', 'night' => 'ለሊት',
                default => $key,
            }
        );
        $locale->method('resolveLocale')->willReturnCallback(
            fn (?string $loc = null) => $loc ?? 'am'
        );

        $calendar = new EthiopicCalendarService();
        $timeService = new EthiopicTimeService($locale);

        $this->formatter = new EthiopicFormatter($calendar, $timeService, $locale);
    }

    // ──────────────────────────────────────────────
    // formatDate() tests
    // ──────────────────────────────────────────────

    public function test_format_date_amharic_no_week(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::AmharicNoWeek);

        self::assertSame('መስከረም 01, 2016', $result);
    }

    public function test_format_date_transliteration_no_week(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::TransliterationNoWeek);

        self::assertSame('Meskerem 01, 2016', $result);
    }

    public function test_format_date_amharic_combined(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::AmharicCombined);

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም', $result);
        self::assertStringContainsString('2016', $result);
        self::assertStringContainsString('/', $result); // separator present
    }

    public function test_format_date_transliteration_combined(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::TransliterationCombined);

        self::assertNotNull($result);
        self::assertStringContainsString('Meskerem', $result);
        self::assertStringContainsString('2016', $result);
    }

    public function test_format_date_hybrid(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::Hybrid);

        self::assertNotNull($result);
        self::assertStringContainsString('Meskerem', $result);
        self::assertStringContainsString('መስከረም', $result);
    }

    public function test_format_date_compact_amharic(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::CompactAmharic);

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም', $result);
        self::assertStringNotContainsString('/', $result);
    }

    public function test_format_date_clean_gregorian(): void
    {
        $result = $this->formatter->formatDate('2023-09-12', DisplayMode::CleanGregorian);

        self::assertSame('2023-09-12', $result);
    }

    public function test_format_date_null_input(): void
    {
        self::assertNull($this->formatter->formatDate(null));
        self::assertNull($this->formatter->formatDate(''));
        self::assertNull($this->formatter->formatDate('   '));
    }

    public function test_format_date_simple_mode_strings(): void
    {
        // Test the high-level simple mode API
        $ethiopic = $this->formatter->formatDate('2023-09-12', 'ethiopic');
        self::assertSame('መስከረም 01, 2016', $ethiopic);

        $gregorian = $this->formatter->formatDate('2023-09-12', 'gregorian');
        self::assertSame('2023-09-12', $gregorian);

        $dual = $this->formatter->formatDate('2023-09-12', 'dual');
        self::assertStringContainsString('Meskerem', $dual);
        self::assertStringContainsString('መስከረም', $dual);
    }

    // ──────────────────────────────────────────────
    // formatTime() tests
    // ──────────────────────────────────────────────

    public function test_format_time_gregorian_mode(): void
    {
        self::assertSame('10:00 AM', $this->formatter->formatTime('10:00', TimeMode::Gregorian));
        self::assertSame('12:00 PM', $this->formatter->formatTime('12:00', TimeMode::Gregorian));
        self::assertSame('12:00 AM', $this->formatter->formatTime('00:00', TimeMode::Gregorian));
        self::assertSame('6:30 PM', $this->formatter->formatTime('18:30', TimeMode::Gregorian));
        self::assertSame('1:05 PM', $this->formatter->formatTime('13:05', TimeMode::Gregorian));
    }

    public function test_format_time_ethiopian_mode(): void
    {
        self::assertSame('ጠዋት 4:00', $this->formatter->formatTime('10:00', TimeMode::Ethiopian));
        self::assertSame('ከሰዓት 6:00', $this->formatter->formatTime('12:00', TimeMode::Ethiopian));
        self::assertSame('ለሊት 6:00', $this->formatter->formatTime('00:00', TimeMode::Ethiopian));
        self::assertSame('ማታ 12:30', $this->formatter->formatTime('18:30', TimeMode::Ethiopian));
    }

    public function test_format_time_dual_mode(): void
    {
        $result = $this->formatter->formatTime('10:00', TimeMode::Dual);

        // Default template: ':gregorian (:ethiopian)'
        self::assertStringContainsString('10:00 AM', $result);
        self::assertStringContainsString('ጠዋት 4:00', $result);
    }

    public function test_format_time_null_input(): void
    {
        self::assertNull($this->formatter->formatTime(null, TimeMode::Gregorian));
        self::assertNull($this->formatter->formatTime('', TimeMode::Ethiopian));
        self::assertNull($this->formatter->formatTime('invalid', TimeMode::Dual));
    }

    // ──────────────────────────────────────────────
    // formatDateTime() tests
    // ──────────────────────────────────────────────

    public function test_format_date_time_with_gregorian_time(): void
    {
        $result = $this->formatter->formatDateTime(
            '2023-09-12 10:30:00',
            DisplayMode::AmharicNoWeek,
            TimeMode::Gregorian,
        );

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም 01, 2016', $result);
        self::assertStringContainsString('10:30 AM', $result);
    }

    public function test_format_date_time_with_ethiopian_time(): void
    {
        $result = $this->formatter->formatDateTime(
            '2023-09-12 10:30:00',
            DisplayMode::AmharicNoWeek,
            TimeMode::Ethiopian,
        );

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም 01, 2016', $result);
        self::assertStringContainsString('ጠዋት 4:30', $result);
    }

    public function test_format_date_time_with_dual_time(): void
    {
        $result = $this->formatter->formatDateTime(
            '2023-09-12 10:30:00',
            DisplayMode::AmharicNoWeek,
            TimeMode::Dual,
        );

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም 01, 2016', $result);
        self::assertStringContainsString('10:30 AM', $result);
        self::assertStringContainsString('ጠዋት 4:30', $result);
    }

    public function test_format_date_time_date_only(): void
    {
        $result = $this->formatter->formatDateTime(
            '2023-09-12',
            DisplayMode::AmharicNoWeek,
            TimeMode::Ethiopian,
        );

        self::assertSame('መስከረም 01, 2016', $result);
    }

    public function test_format_date_time_null_input(): void
    {
        self::assertNull($this->formatter->formatDateTime(null));
        self::assertNull($this->formatter->formatDateTime(''));
        self::assertNull($this->formatter->formatDateTime('   '));
    }

    // ──────────────────────────────────────────────
    // Month/Day name accessors
    // ──────────────────────────────────────────────

    public function test_get_month_name(): void
    {
        self::assertSame('መስከረም', $this->formatter->getMonthName(1, 'am'));
        self::assertSame('Meskerem', $this->formatter->getMonthName(1, 'en'));
        self::assertSame('ጳጉሜ', $this->formatter->getMonthName(13, 'am'));
        self::assertSame('Pagume', $this->formatter->getMonthName(13, 'en'));
    }

    public function test_get_day_name(): void
    {
        self::assertSame('ሰኞ', $this->formatter->getDayName(1, 'long', 'am'));
        self::assertSame('Monday', $this->formatter->getDayName(1, 'long', 'en'));
        self::assertSame('እሁድ', $this->formatter->getDayName(7, 'long', 'am'));
        self::assertSame('Sunday', $this->formatter->getDayName(7, 'long', 'en'));
    }
}
