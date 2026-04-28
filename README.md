# Ethiopian Calendar & Time Engine for Filament

**Finally, Ethiopian dates and time — done right in Filament.**

Production-ready Ethiopian calendar and Ethiopian time support for Laravel + Filament.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mammesat/filament-ethiopic-calendar.svg?style=flat-square)](https://packagist.org/packages/mammesat/filament-ethiopic-calendar)
[![Total Downloads](https://img.shields.io/packagist/dt/mammesat/filament-ethiopic-calendar.svg?style=flat-square)](https://packagist.org/packages/mammesat/filament-ethiopic-calendar)
[![Tests](https://img.shields.io/github/actions/workflow/status/mammesat/filament-ethiopic-calendar/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mammesat/filament-ethiopic-calendar/actions?query=workflow%3ATests+branch%3Amain)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)

---

## Why this package?

Ethiopian users do not experience date and time the same way as standard Gregorian interfaces.

- AM/PM alone is not enough for real Ethiopian-local products.
- Ethiopian users often expect Ethiopian time (6-hour shift), not only Gregorian clock output.
- Calendar conversion without Ethiopian time still creates UX confusion in real workflows.

This package solves both problems together, with one consistent formatting engine across forms, tables, and infolists.

---

## What makes it different

- ✅ Ethiopian ↔ Gregorian date conversion
- ✅ **Ethiopian time system (6-hour shift)**
- ✅ Dual output mode (Ethiopian + Gregorian)
- ✅ Amharic and English Ethiopian labels
- ✅ Filament-native components with one formatter as SSOT

---

## Output example (dual mode)

```text
Gregorian: Apr 21, 2026 10:00 AM
Ethiopian: Miyazya 13, 2018 4:00 ጠዋት
Dual: Apr 21, 2026 (Miyazya 13, 2018)
      10:00 AM (4:00 ጠዋት)
```

---

## Installation

```bash
composer require mammesat/filament-ethiopic-calendar
```

Publish config:

```bash
php artisan vendor:publish --tag="filament-ethiopic-calendar-config"
```

Clear caches after config changes:

```bash
php artisan optimize:clear
```

---

## Quick Start (< 60 seconds)

### Form Field

```php
use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;

EthiopicDateTimePicker::make('appointment_at')
    ->label('Appointment Date')
    ->ethiopic()               // Sets to ethiopic_amharic & ethiopianTime
    ->withTime()               // Enables time selector
    ->required();
```

### Table Column

```php
use Mammesat\FilamentEthiopicCalendar\Tables\Columns\EthiopicDateColumn;

EthiopicDateColumn::make('appointment_at')
    ->label('Appointment')
    ->dual()                   // Outputs dual dates
    ->dualTime()               // Outputs dual time
    ->withTime();
```

### Infolist Entry

```php
use Mammesat\FilamentEthiopicCalendar\Infolists\Components\EthiopicDateEntry;

EthiopicDateEntry::make('appointment_at')
    ->ethiopicDisplayMode('ethiopic_amharic') // Manual override if needed
    ->ethiopianTime()
    ->withTime();
```

---

## Formatter API

`EthiopicFormatter` is the single source of truth for output:

```php
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicFormatter;

EthiopicFormatter::formatDate('2026-04-21', 'ethiopic_amharic');
EthiopicFormatter::formatTime('10:00:00', 'dual');
EthiopicFormatter::formatDateTime('2026-04-21 10:00:00', 'dual', 'dual');
```

---

## Screenshots

> You can replace these with your own panel captures for your marketplace listing.

- Settings / Configuration Panel
  ![Settings Panel](art/settings-panel.png)

- Form Picker + helper output
  ![Form Picker](art/form-picker.png)

- Table output with dual display
  ![Table Output](art/data-table.png)

- Infolist scenarios
  ![Infolist](art/infolist-test-scenarios.png)

---

## Configuration

Global defaults live in `config/ethiopic-calendar.php`.

Common options:

- `display_mode`
- `calendar_locale`
- `with_time`
- `time_mode`
- `dual_time_format`
- `timezone`

Per-field overrides always win over global config.

---

## Backward Compatibility

> **Note:** Legacy display mode values (e.g., `amharic_no_week`, `clean_gregorian`, `hybrid`) are still fully supported and automatically normalized at runtime across the platform. You do not need to forcibly migrate existing database records or settings.

---

## License

MIT
