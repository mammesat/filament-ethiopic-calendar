<?php

namespace Workbench\App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Workbench\App\Models\TestSetting;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class TestSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Test Settings';
    protected static ?string $title = 'Test Settings';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Dates'; // Based on typical test setup groupings
    protected static ?int $navigationSort = 10;
    
    // We bind local data keys to be populated and tracked by Livewire state
    public ?array $data = [];

    protected string $view = 'workbench::filament.pages.test-settings';

    public function mount(): void
    {
        $setting = TestSetting::current();

        $this->getForm('form')->fill([
            'display_mode' => $setting->display_mode,
            'calendar_locale' => $setting->calendar_locale,
            'with_time' => $setting->with_time,
        ]);
    }

    public function form($form)
    {
        return $form
            ->schema([
                Select::make('display_mode')
                    ->label('Display Mode')
                    ->options([
                        'amharic_combined' => 'amharic_combined',
                        'transliteration_combined' => 'transliteration_combined',
                        'hybrid' => 'hybrid',
                        'compact_amharic' => 'compact_amharic',
                        'clean_gregorian' => 'clean_gregorian',
                        'amharic_no_week' => 'amharic_no_week',
                        'transliteration_no_week' => 'transliteration_no_week',
                    ])
                    ->required(),

                Select::make('calendar_locale')
                    ->label('Calendar Locale')
                    ->options([
                        'am' => 'Amharic (am)',
                        'en' => 'English (en)',
                    ])
                    ->required(),

                Toggle::make('with_time')
                    ->label('Enable Time Picker'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->getForm('form')->getState();

        $setting = TestSetting::current();
        $setting->update([
            'display_mode' => $data['display_mode'],
            'calendar_locale' => $data['calendar_locale'],
            'with_time' => $data['with_time'],
        ]);

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }
}
