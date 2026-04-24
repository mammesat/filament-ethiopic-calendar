<?php

namespace Workbench\App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Infolists\Components\EthiopicDateEntry;

class TestInfolist extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationLabel = 'Test Infolist';
    protected static ?string $title = 'Test Infolist';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Pages';

    protected string $view = 'workbench::filament.pages.test-infolist';

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            'mode' => 'amharic_combined',
            'with_time' => false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Settings')
                    ->components([
                        Select::make('mode')
                            ->options([
                                'amharic_combined' => 'AmharicCombined',
                                'transliteration_combined' => 'TransliterationCombined',
                                'hybrid' => 'Hybrid',
                                'compact_amharic' => 'CompactAmharic',
                                'clean_gregorian' => 'CleanGregorian',
                                'amharic_no_week' => 'AmharicNoWeek',
                                'transliteration_no_week' => 'TransliterationNoWeek',
                            ])
                            ->default('amharic_combined')
                            ->live(),
                        Toggle::make('with_time')
                            ->label('Enable Ethiopian Time')
                            ->default(false)
                            ->live(),
                    ])->columns(2)
            ])->statePath('formData');
    }

    protected function getTestRecord(): array
    {
        return [
            'birth_date' => '2024-09-11',
            'appointment_datetime' => '2024-09-11 14:30:00',
            'pagume_date' => '2023-09-11', // edge case
            'null_date' => null,
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $mode = $this->formData['mode'] ?? 'amharic_combined';
        $withTime = $this->formData['with_time'] ?? false;

        $resolvedMode = DisplayMode::tryFrom($mode) ?? DisplayMode::AmharicCombined;

        return $schema
            ->components([
                Section::make('Infolist Test Scenarios')
                    ->description('Testing EthiopicDateEntry across various configurations.')
                    ->components([
                        EthiopicDateEntry::make('birth_date')
                            ->label('Basic Date Display')
                            ->displayMode($resolvedMode)
                            ->withTime(false),

                        EthiopicDateEntry::make('appointment_datetime')
                            ->label('DateTime Display (with_time)')
                            ->displayMode($resolvedMode)
                            ->withTime($withTime)
                            ->timeMode($withTime ? 'ethiopian' : 'gregorian'),

                        EthiopicDateEntry::make('pagume_date')
                            ->label('Edge Case: Pagume Test')
                            ->displayMode($resolvedMode),

                        EthiopicDateEntry::make('null_date')
                            ->label('Edge Case: Null Date')
                            ->displayMode($resolvedMode),
                    ])
            ])->state($this->getTestRecord());
    }
}
