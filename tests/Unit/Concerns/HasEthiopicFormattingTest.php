<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit\Concerns;

use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicFormatting;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class HasEthiopicFormattingTest extends TestCase
{
    private object $mock;

    protected function setUp(): void
    {
        parent::setUp();

        // Standardize timezones for testing
        Config::set('app.timezone', 'Africa/Addis_Ababa');
        Config::set('ethiopic-calendar.timezone', 'Africa/Addis_Ababa');

        $this->mock = new class {
            use HasEthiopicFormatting;

            public function getTooltip(mixed $state): ?string
            {
                return $this->getAlternateTooltip($state);
            }
        };
    }

    public function test_it_returns_null_if_tooltip_is_disabled(): void
    {
        $this->mock->tooltipAlternate(false);
        $this->assertNull($this->mock->getTooltip('2024-09-11'));
    }

    public function test_it_returns_null_for_dual_mode(): void
    {
        $this->mock->tooltipAlternate(true);
        $this->mock->displayMode(DisplayMode::Dual);
        
        $this->assertNull($this->mock->getTooltip('2024-09-11'));
    }

    public function test_it_flips_gregorian_to_ethiopic(): void
    {
        $this->mock->tooltipAlternate(true);
        $this->mock->displayMode(DisplayMode::Gregorian);
        $this->mock->withTime(false);

        $tooltip = $this->mock->getTooltip('2024-05-23');
        
        // 2024-05-23 Gregorian is 2016-09-15 Ethiopic (ግንቦት 15, 2016)
        $this->assertStringContainsString('ግንቦት 15, 2016', $tooltip);
    }

    public function test_it_flips_ethiopic_to_gregorian(): void
    {
        $this->mock->tooltipAlternate(true);
        $this->mock->displayMode(DisplayMode::EthiopicAmharic);
        $this->mock->withTime(false);

        $tooltip = $this->mock->getTooltip('2024-05-23');
        
        $this->assertStringContainsString('May', $tooltip);
        $this->assertStringContainsString('23', $tooltip);
        $this->assertStringContainsString('2024', $tooltip);
    }

    public function test_it_handles_datetime_flipping(): void
    {
        $this->mock->tooltipAlternate(true);
        $this->mock->displayMode(DisplayMode::Gregorian);
        $this->mock->withTime(true);

        // 08:30 Gregorian + 6 hours = 14:30 -> 2:30 Ethiopian
        $tooltip = $this->mock->getTooltip('2024-05-23 08:30:00');
        
        $this->assertStringContainsString('ጠዋት 2:30', $tooltip);
        $this->assertStringContainsString('ግንቦት 15, 2016', $tooltip);
    }

    public function test_it_returns_null_for_invalid_state(): void
    {
        $this->mock->tooltipAlternate(true);
        $this->assertNull($this->mock->getTooltip(null));
        $this->assertNull($this->mock->getTooltip('invalid-date'));
    }
}
