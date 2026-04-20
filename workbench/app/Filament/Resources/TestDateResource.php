<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Mammesat\FilamentEthiopicDatePicker\Forms\Components\EthiopicDatePicker;
use Mammesat\FilamentEthiopicDatePicker\Tables\Columns\EthiopicDateColumn;
use Workbench\App\Filament\Resources\TestDateResource\Pages;
use Workbench\App\Models\TestDate;

class TestDateResource extends Resource
{
    protected static ?string $model = TestDate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Test Dates';

    protected static ?string $modelLabel = 'Test Date';

    protected static ?string $pluralModelLabel = 'Test Dates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                EthiopicDatePicker::make('birth_date')
                    ->label('Birth Date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Birth Date (Gregorian)')
                    ->date()
                    ->sortable(),
                EthiopicDateColumn::make('birth_date')
                    ->label('Birth Date (Ethiopian)')
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
