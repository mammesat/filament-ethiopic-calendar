<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;
use Mammesat\FilamentEthiopicCalendar\Tables\Columns\EthiopicDateColumn;
use Workbench\App\Filament\Resources\TestDateResource\Pages;
use Workbench\App\Models\TestDate;
use Workbench\App\Models\TestSetting;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;

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
                    ->calendarLocale($settings->calendar_locale)
                    ->withTime($settings->with_time)
                    ->required()
                    ->live()
                    ->helperText(
                        fn($state) => $state
                            ? app(EthiopicFormatter::class)->formatDateTime($state, $settings->display_mode, $settings->time_mode)
                            : null
                    ),
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
                    ->label('Birth Date (Gregorian)')
                    ->formatStateUsing(
                        fn($state) => $settings->with_time
                            ? \Carbon\Carbon::parse($state)->format('M, j Y g:i A')
                            : \Carbon\Carbon::parse($state)->format('M, j Y')
                    )
                    ->sortable(),
                EthiopicDateColumn::make('birth_date_ethiopic')
                    ->getStateUsing(fn($record) => $record->birth_date)
                    ->label('Birth Date (Ethiopian)')
                    ->displayMode($settings->display_mode)
                    ->timeMode($settings->time_mode)
                    ->withTime($settings->with_time)
                    ->sortable(),
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
