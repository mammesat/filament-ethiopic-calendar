<?php

namespace Workbench\App\Filament\Pages;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Workbench\App\Models\TestSetting;

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
            'display_mode' => $setting->display_mode ?? 'ethiopic_amharic',
            'time_mode' => $setting->time_mode ?? 'gregorian',
            'calendar_locale' => in_array($setting->calendar_locale, ['am', 'en'], true) ? $setting->calendar_locale : 'am',
            'with_time' => (bool) $setting->with_time,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Calendar System')
                    ->description('Choose how dates should appear in your forms, tables, and infolists.')
                    ->icon('heroicon-o-calendar-days')
                    ->components([
                        Select::make('display_mode')
                            ->label('Date Display')
                            ->options([
                                'ethiopic_amharic' => 'Ethiopian',
                                'gregorian' => 'Gregorian',
                                'dual' => 'Dual (Ethiopian + Gregorian)',
                            ])
                            ->helperText('Choose how dates are shown to users.')
                            ->required()
                            ->live(),
                    ]),

                Section::make('Time System')
                    ->description('This plugin includes the full Ethiopian time system (6-hour shift), not just AM/PM formatting.')
                    ->icon('heroicon-o-clock')
                    ->components([
                        Select::make('time_mode')
                            ->label('Time Display')
                            ->options([
                                'gregorian' => 'Gregorian (10:00 AM)',
                                'ethiopian' => 'Ethiopian (4:00 ጠዋት)',
                                'dual' => 'Dual (10:00 AM + 4:00 ጠዋት)',
                            ])
                            ->helperText('Dual mode is recommended for applications that support both Ethiopian and Gregorian users.')
                            ->required()
                            ->live(),
                    ]),

                Section::make('Localization')
                    ->description('Pick the language for Ethiopian month and day names.')
                    ->icon('heroicon-o-language')
                    ->components([
                        Select::make('calendar_locale')
                            ->label('Ethiopian Language')
                            ->options([
                                'am' => 'Ethiopian (Amharic)',
                                'en' => 'Ethiopian (English)',
                            ])
                            ->helperText('Used for Ethiopian labels in Ethiopian and dual display modes.')
                            ->required()
                            ->live(),
                    ]),

                Section::make('Behavior')
                    ->description('Fine-tune whether date-only or date-and-time values are displayed.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->components([
                        Toggle::make('with_time')
                            ->label('Include time in output')
                            ->helperText('Enable this to show both calendar and Ethiopian time output together.')
                            ->live(),
                    ]),

                Section::make('Live Preview')
                    ->description('Instantly preview Gregorian, Ethiopian, and Dual output for the same datetime value.')
                    ->icon('heroicon-o-sparkles')
                    ->components([
                        Placeholder::make('preview')
                            ->label('Example datetime: 2026-04-21 10:00:00')
                            ->content(fn ($get) => view('workbench::filament.components.live-preview', $this->buildPreviewData($get))),
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
            'display_mode' => $data['display_mode'] ?? 'ethiopic_amharic',
            'time_mode' => $data['time_mode'] ?? 'gregorian',
            'calendar_locale' => in_array($data['calendar_locale'] ?? 'am', ['am', 'en'], true) ? $data['calendar_locale'] : 'am',
            'with_time' => (bool) ($data['with_time'] ?? false),
        ]);

        Notification::make()
            ->title('Settings saved')
            ->body('Calendar, time, and localization preferences have been updated.')
            ->success()
            ->send();
    }

    private function buildPreviewData(callable $get): array
    {
        $displayMode = (string) ($get('display_mode') ?? 'ethiopic_amharic');
        $timeMode = (string) ($get('time_mode') ?? 'gregorian');
        $locale = in_array($get('calendar_locale'), ['am', 'en'], true) ? $get('calendar_locale') : 'am';
        $withTime = (bool) $get('with_time');

        $formatter = app(EthiopicFormatter::class);

        config(['ethiopic-calendar.calendar_locale' => $locale]);

        $previewDateTime = $withTime ? '2026-04-21 10:00:00' : '2026-04-21';

        $ethiopicMode = $locale === 'en' ? 'ethiopic_english' : 'ethiopic_amharic';

        $lines = [
            'gregorian' => $formatter->formatDateTime($previewDateTime, 'gregorian', $timeMode),
            'ethiopian' => $formatter->formatDateTime($previewDateTime, $ethiopicMode, $timeMode),
            'dual' => $formatter->formatDateTime($previewDateTime, 'dual', $timeMode),
        ];

        return [
            'activeMode' => $displayMode,
            'activeTimeMode' => $timeMode,
            'locale' => $locale,
            'withTime' => $withTime,
            'lines' => $lines,
            'activeLine' => $lines[$displayMode] ?? null,
        ];
    }
}
