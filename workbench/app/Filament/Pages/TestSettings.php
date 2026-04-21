<?php

namespace Workbench\App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Workbench\App\Models\TestSetting;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TestSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Calendar & Time Settings';
    protected static ?string $title = 'Calendar & Time Settings';
    protected static string | \UnitEnum | null $navigationGroup = 'Test Dates';
    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    protected string $view = 'workbench::filament.pages.test-settings';

    public function mount(): void
    {
        $setting = TestSetting::current();

        $this->getForm('form')->fill([
            'display_mode' => $setting->display_mode,
            'time_mode' => $setting->time_mode,
            'calendar_locale' => $setting->calendar_locale,
            'with_time' => $setting->with_time,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Calendar System')
                    ->icon('heroicon-o-calendar')
                    ->components([
                        Select::make('display_mode')
                            ->label('Calendar Display')
                            ->options([
                                'ethiopic' => 'Ethiopic',
                                'gregorian' => 'Gregorian',
                                'dual' => 'Dual',
                            ])
                            ->helperText('Controls how dates are displayed across the system.')
                            ->required()
                            ->live(),
                    ]),

                Section::make('Time System')
                    ->icon('heroicon-o-clock')
                    ->components([
                        Select::make('time_mode')
                            ->label('Time System')
                            ->options([
                                'gregorian' => 'Gregorian (10:00 AM)',
                                'ethiopian' => 'Ethiopian (4:00 ጠዋት)',
                                'dual' => 'Dual (10:00 AM (4:00 ጠዋት))',
                            ])
                            ->helperText('Defines how time is interpreted and displayed.')
                            ->required()
                            ->live(),
                    ]),

                Section::make('Localization')
                    ->icon('heroicon-o-globe-alt')
                    ->components([
                        Select::make('calendar_locale')
                            ->label('Calendar Locale')
                            ->options([
                                'am' => 'Amharic',
                                'en' => 'English',
                            ])
                            ->required()
                            ->live(),
                    ]),

                Section::make('Behavior')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->components([
                        Toggle::make('with_time')
                            ->label('Include Time')
                            ->live(),
                    ]),

                Section::make('Live Preview')
                    ->icon('heroicon-o-eye')
                    ->components([
                        Placeholder::make('preview')
                            ->label('Current Output Example')
                            ->content(function ($get) {
                                $formatter = app(EthiopicFormatter::class);

                                $date = '2026-04-21 10:00:00';

                                // If they don't include time, we should only pass date? 
                                // No, just let Formatter handle it or strip time manually if with_time is false.
                                // Actually, if with_time is false, formatDateTime just formats what we pass. 
                                // To simulate realistic output, we pass datetime if with_time is true, else date only.
                                if (! $get('with_time')) {
                                    $date = '2026-04-21';
                                }

                                return view('workbench::filament.components.live-preview', [
                                    'displayMode' => $get('display_mode'),
                                    'timeMode' => $get('time_mode'),
                                    'withTime' => $get('with_time'),
                                ]);
                            }),
                    ]),
            ])
            ->statePath('data')
            ->columns(1);
    }

    public function save(): void
    {
        $data = $this->getForm('form')->getState();

        $setting = TestSetting::current();
        $setting->update([
            'display_mode' => $data['display_mode'],
            'time_mode' => $data['time_mode'],
            'calendar_locale' => $data['calendar_locale'],
            'with_time' => $data['with_time'],
        ]);

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }
}
