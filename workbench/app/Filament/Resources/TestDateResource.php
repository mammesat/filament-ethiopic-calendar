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
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;
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

        return $schema
            ->components([
                EthiopicDateTimePicker::make('birth_date')
                    ->label('Birth Date')
                    ->displayMode($settings->display_mode)
                    ->timeMode($settings->time_mode)
                    ->calendarLocale($settings->calendar_locale ?? 'am')
                    ->withTime((bool) $settings->with_time)
                    ->required()
                    ->live(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $settings = TestSetting::current();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Display Output')
                    ->formatStateUsing(function ($state) use ($settings): HtmlString {
                        $dateTime = Carbon::parse($state, config('app.timezone'))
                            ->setTimezone(EthiopicConfig::timezone())
                            ->format($settings->with_time ? 'Y-m-d H:i:s' : 'Y-m-d');

                        $formatted = app(EthiopicFormatter::class)->formatDateTime(
                            $dateTime,
                            $settings->display_mode,
                            $settings->time_mode,
                        ) ?? '—';

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
}
