<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources;

use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Workbench\App\Filament\Resources\TestDateResource\Pages;
use Workbench\App\Models\TestDate;
use Workbench\App\Models\TestSetting;

class TestDateResource extends Resource
{
    protected static ?string $model = TestDate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Test Dates';

    protected static ?string $modelLabel = 'Test Date';

    protected static ?string $pluralModelLabel = 'Test Dates';

    public static function form(Schema $schema): Schema
    {
        $settings = TestSetting::current();
        static::applyRuntimeLocale($settings->calendar_locale);

        return $schema
            ->components([
                EthiopicDateTimePicker::make('birth_date')
                    ->label('Birth Date')
                    ->displayMode(static::normalizeDisplayMode($settings->display_mode))
                    ->timeMode(static::normalizeTimeMode($settings->time_mode))
                    ->calendarLocale(in_array($settings->calendar_locale, ['am', 'en'], true) ? $settings->calendar_locale : 'am')
                    ->withTime((bool) $settings->with_time)
                    ->required()
                    ->live()
                    ->helperText(function ($state) use ($settings): ?string {
                        if (! $state) {
                            return null;
                        }

                        $displayMode = static::normalizeDisplayMode($settings->display_mode);
                        $timeMode = static::normalizeTimeMode($settings->time_mode);
                        $locale = in_array($settings->calendar_locale, ['am', 'en'], true) ? $settings->calendar_locale : 'am';

                        static::applyRuntimeLocale($locale);

                        $dateTime = Carbon::parse($state, config('app.timezone'))
                            ->setTimezone(\Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig::timezone())
                            ->format($settings->with_time ? 'Y-m-d H:i:s' : 'Y-m-d');

                        $formatted = app(EthiopicFormatter::class)->formatDateTime(
                            $dateTime,
                            $displayMode,
                            $timeMode,
                        );

                        return 'Will be displayed as: ' . $formatted;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        $settings = TestSetting::current();
        static::applyRuntimeLocale($settings->calendar_locale);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Display Output')
                    ->formatStateUsing(function ($state) use ($settings): HtmlString {
                        $dateTime = Carbon::parse($state, config('app.timezone'))
                            ->setTimezone(\Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig::timezone())
                            ->format($settings->with_time ? 'Y-m-d H:i:s' : 'Y-m-d');

                        $formatted = app(EthiopicFormatter::class)->formatDateTime(
                            $dateTime,
                            static::normalizeDisplayMode($settings->display_mode),
                            static::normalizeTimeMode($settings->time_mode),
                        ) ?? '—';

                        if ((bool) $settings->with_time && static::normalizeDisplayMode($settings->display_mode) === 'dual' && static::normalizeTimeMode($settings->time_mode) === 'dual') {
                            $formatted = preg_replace('/\)\s+/', ")\n", $formatted, 1) ?? $formatted;
                        }

                        return new HtmlString(nl2br(e($formatted)));
                    })
                    ->html()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gregorian_birth_date')
                    ->label('Stored Value (Gregorian)')
                    ->getStateUsing(fn ($record) => $record->birth_date)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(
                        fn ($state) => $settings->with_time
                            ? Carbon::parse($state)->format('M j, Y g:i A')
                            : Carbon::parse($state)->format('M j, Y')
                    )
                    ->sortable(query: fn (\Illuminate\Database\Eloquent\Builder $query, string $direction) => $query->orderBy('birth_date', $direction)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestDates::route('/'),
            'create' => Pages\CreateTestDate::route('/create'),
            'edit' => Pages\EditTestDate::route('/{record}/edit'),
        ];
    }

    private static function applyRuntimeLocale(?string $locale): void
    {
        config(['ethiopic-calendar.calendar_locale' => in_array($locale, ['am', 'en'], true) ? $locale : 'am']);
    }

    private static function normalizeDisplayMode(?string $mode): string
    {
        $resolved = \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::fromLegacy($mode ?? '') 
            ?? \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::tryFrom($mode ?? '');

        if ($resolved) {
            return match ($resolved) {
                \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::EthiopicAmharic, \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::EthiopicEnglish, \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::AmharicCombined, \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::TransliterationCombined, \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::CompactAmharic => 'ethiopic_amharic',
                \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::Gregorian => 'gregorian',
                \Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode::Dual => 'dual',
            };
        }

        return match ($mode) {
            'gregorian', 'clean_gregorian' => 'gregorian',
            'dual', 'hybrid' => 'dual',
            default => 'ethiopic_amharic',
        };
    }

    private static function normalizeTimeMode(?string $mode): string
    {
        return in_array($mode, ['gregorian', 'ethiopian', 'dual'], true) ? $mode : 'gregorian';
    }
}
