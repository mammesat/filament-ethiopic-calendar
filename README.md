# Filament Ethiopic Date Picker

A Composer-installable **Filament v5** plugin that provides an Ethiopic calendar form field and converts values to Gregorian dates for storage.

## Features

- Custom Filament field component (no `DatePicker` extension)
- Ethiopic ⇄ Gregorian conversion via JDN algorithm
- Gregorian `Y-m-d` storage output
- Ethiopic `Y-m-d` display input
- Null-safe conversion helpers
- Laravel package auto-discovery support

## Installation

```bash
composer require mammesat/filament-ethiopic-date-picker
```

## Usage

Use the field in any Filament form schema:

```php
use Mammesat\FilamentEthiopicDatePicker\Forms\Components\EthiopicDatePicker;

EthiopicDatePicker::make('birth_date')
    ->label('Birth Date (Ethiopic)');
```

## Conversion Service

You can also resolve and use the conversion service directly:

```php
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;

$calendar = app(EthiopicCalendar::class);

$ethiopic = $calendar->toEthiopicString('2023-09-12'); // 2016-01-01
$gregorian = $calendar->toGregorianString('2017-01-01'); // 2024-09-11
```

## Published Config

```bash
php artisan vendor:publish --tag=filament-ethiopic-date-picker-config
```

Config file: `config/ethiopic-calendar.php`
