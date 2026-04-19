<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;

class EthiopicDatePicker extends Field
{
    protected string $view = 'filament-ethiopic-date-picker::forms.components.ethiopic-date-picker';

    protected int|Closure|null $minYear = null;

    protected int|Closure|null $maxYear = null;

    private ?int $resolvedCurrentYear = null;

    public function minYear(int|Closure|null $year): static
    {
        $this->minYear = $year;

        return $this;
    }

    public function maxYear(int|Closure|null $year): static
    {
        $this->maxYear = $year;

        return $this;
    }

    /**
     * @return array<int,int>
     */
    public function getYearOptions(): array
    {
        $minYear = $this->getMinYear();
        $maxYear = $this->getMaxYear();

        if ($minYear > $maxYear) {
            [$minYear, $maxYear] = [$maxYear, $minYear];
        }

        return array_combine(range($minYear, $maxYear), range($minYear, $maxYear)) ?: [];
    }

    public function getMinYear(): int
    {
        $defaultYear = $this->resolveCurrentEthiopicYear() - 100;

        return (int) ($this->evaluate($this->minYear) ?? $defaultYear);
    }

    public function getMaxYear(): int
    {
        $defaultYear = $this->resolveCurrentEthiopicYear() + 25;

        return (int) ($this->evaluate($this->maxYear) ?? $defaultYear);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(function (mixed $state): ?string {
            if (! is_string($state) || trim($state) === '') {
                return null;
            }

            return app(EthiopicCalendar::class)->toEthiopicString($state);
        });

        $this->dehydrateStateUsing(function (mixed $state): ?string {
            if (! is_string($state) || trim($state) === '') {
                return null;
            }

            return app(EthiopicCalendar::class)->toGregorianString($state);
        });

        $this->rule('nullable');
        $this->rule('regex:/^\d{4}-\d{2}-\d{2}$/');
        $this->rule(static function (): Closure {
            return static function (string $attribute, mixed $value, Closure $fail): void {
                if (! is_string($value) || trim($value) === '') {
                    return;
                }

                if (! app(EthiopicCalendar::class)->isValidEthiopicDateString($value)) {
                    $fail("The {$attribute} must be a valid Ethiopic date.");
                }
            };
        });
    }

    private function resolveCurrentEthiopicYear(): int
    {
        if ($this->resolvedCurrentYear !== null) {
            return $this->resolvedCurrentYear;
        }

        $today = getdate();

        $this->resolvedCurrentYear = app(EthiopicCalendar::class)
            ->toEthiopic($today['year'], $today['mon'], $today['mday'])['year'];

        return $this->resolvedCurrentYear;
    }
}
