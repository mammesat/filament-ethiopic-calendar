# Ethiopian Calendar & Time Engine for Filament

**Finally, Ethiopian dates and time — done right in Filament.**

Production-ready Ethiopian calendar and Ethiopian time support for Laravel + Filament v5.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mammesat/filament-ethiopic-calendar.svg?style=flat-square)](https://packagist.org/packages/mammesat/filament-ethiopic-calendar)
[![Total Downloads](https://img.shields.io/packagist/dt/mammesat/filament-ethiopic-calendar.svg?style=flat-square)](https://packagist.org/packages/mammesat/filament-ethiopic-calendar)
[![Tests](https://img.shields.io/github/actions/workflow/status/mammesat/filament-ethiopic-calendar/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mammesat/filament-ethiopic-calendar/actions?query=workflow%3ATests+branch%3Amain)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)

---

## 🚀 Quick Start (30 seconds)

### Install

```bash
composer require mammesat/filament-ethiopic-calendar
```

### Use

```php
use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;

EthiopicDateTimePicker::make('appointment_at')
    ->label('Appointment Date')
    ->ethiopic()
    ->withTime()
    ->required();
```

**That's it.** No configuration required. Works out of the box.

---

## 🧠 What `->ethiopic()` does

Calling `->ethiopic()` automatically configures:

| Setting | Value | Effect |
|---------|-------|--------|
| `displayMode` | `ethiopic_amharic` | Ethiopian date labels in Amharic |
| `timeMode` | `ethiopian` | Ethiopian time system (6-hour shift) |
| `calendarLocale` | `am` | Amharic month/day names in the calendar popup |

You do **not** need to set these manually. One method handles everything.

---

## 👀 Expected UI

After adding `->ethiopic()->withTime()`, you should see:

- ✅ **Ethiopian calendar popup** with Amharic month and day names
- ✅ **Ethiopian time display** (e.g., `ጠዋት 4:00` instead of `10:00 AM`)
- ✅ **Helper preview** below the field showing the Ethiopian date/time
- ✅ **"Stored as: Gregorian"** note so developers know the DB format
- ✅ Standard Filament DateTimePicker UI (no custom dropdowns)

---

## 📦 All Three Component Types

### Form Field

```php
use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;

EthiopicDateTimePicker::make('birth_date')
    ->label('Birth Date')
    ->ethiopic()
    ->withTime()
    ->required();
```

### Table Column

```php
use Mammesat\FilamentEthiopicCalendar\Tables\Columns\EthiopicDateColumn;

EthiopicDateColumn::make('birth_date')
    ->label('Birth Date')
    ->ethiopic()
    ->withTime();
```

### Infolist Entry

```php
use Mammesat\FilamentEthiopicCalendar\Infolists\Components\EthiopicDateEntry;

EthiopicDateEntry::make('birth_date')
    ->label('Birth Date')
    ->ethiopic()
    ->withTime();
```

All three share the same API. Use `->ethiopic()`, `->dual()`, or `->gregorian()` on any of them.

---

## ⚙️ Optional Customization

Most users only need `->ethiopic()`. But if you need more control:

### Dual mode (Ethiopian + Gregorian side by side)

```php
EthiopicDateTimePicker::make('date')
    ->dual()
    ->withTime();
```

Output: `Apr 21, 2026 (ሚያዝያ 13, 2018) 10:00 AM (ጠዋት 4:00)`

### Gregorian mode

```php
EthiopicDateTimePicker::make('date')
    ->gregorian();
```

### English transliteration

```php
EthiopicDateTimePicker::make('date')
    ->ethiopic()
    ->calendarLocale('en');
```

### Custom helper text

```php
EthiopicDateTimePicker::make('date')
    ->ethiopic()
    ->showEthiopicHelper(false)  // disable built-in helper
    ->helperText(fn ($state, $component) =>
        $state
            ? 'Displayed as: ' . $component->getFormattedPreview($state)
            : null
    );
```

### Date only (no time picker)

```php
EthiopicDateTimePicker::make('date')
    ->ethiopic();  // no ->withTime() = date only
```

---

## 🔧 Global Configuration (Optional)

Most projects don't need this. But if you want to set defaults globally:

```bash
php artisan vendor:publish --tag="filament-ethiopic-calendar-config"
```

This publishes `config/ethiopic-calendar.php` where you can set:

- `display_mode` — default display mode (`ethiopic_amharic`, `gregorian`, `dual`)
- `time_mode` — default time system (`gregorian`, `ethiopian`, `dual`)
- `calendar_locale` — default popup language (`am`, `en`)
- `with_time` — enable time globally (`true` / `false`)
- `timezone` — defaults to `Africa/Addis_Ababa`

Per-field settings (e.g., `->ethiopic()`) always override global config.

---

## ❗ Common Mistakes

### Using Filament's `DateTimePicker` instead of `EthiopicDateTimePicker`

```php
// ❌ Wrong — this is Filament's standard picker, no Ethiopian support
DateTimePicker::make('date');

// ✅ Correct
EthiopicDateTimePicker::make('date')->ethiopic();
```

### Assets not loading

If the calendar doesn't render properly after install:

```bash
php artisan filament:assets
php artisan optimize:clear
```

### Manually configuring what `->ethiopic()` already does

```php
// ❌ Unnecessary — don't do this
EthiopicDateTimePicker::make('date')
    ->displayMode('ethiopic_amharic')
    ->timeMode('ethiopian')
    ->calendarLocale('am');

// ✅ Just use the preset
EthiopicDateTimePicker::make('date')
    ->ethiopic();
```

---

## 📐 Formatter API

For use outside Filament components (e.g., Blade views, exports, notifications):

```php
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicFormatter;

// Date only
EthiopicFormatter::formatDate('2026-04-21', 'ethiopic_amharic');
// → "ሚያዝያ 13, 2018"

// Date + time
EthiopicFormatter::formatDateTime('2026-04-21 10:00:00', 'dual', 'dual');
// → "Apr 21, 2026 (ሚያዝያ 13, 2018) 10:00 AM (ጠዋት 4:00)"

// Ethiopian time only
EthiopicFormatter::formatEthiopianTime('10:00');
// → "ጠዋት 4:00"
```

---

## 🔄 Backward Compatibility

> Legacy display mode values (e.g., `amharic_no_week`, `clean_gregorian`, `hybrid`) are still fully supported and automatically normalized at runtime. You do not need to migrate existing database records or settings.

---

## 📸 Screenshots

- Settings / Configuration Panel
  ![Settings Panel](art/settings-panel.png)

- Form Picker + helper output
  ![Form Picker](art/form-picker.png)

- Table output with dual display
  ![Table Output](art/data-table.png)

- Infolist scenarios
  ![Infolist](art/infolist-test-scenarios.png)

---

## License

MIT
