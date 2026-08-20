# Changelog

All notable changes to `filament-ethiopic-calendar` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-08-20

### Added
- `tooltipAlternate()` macro for `TextColumn` and `TextEntry` — hover to see the opposite calendar system
- Interactive tooltip automatically flips between Ethiopic and Gregorian display

### Changed
- **BREAKING**: Removed deprecated `Forms\Components\EthiopicDatePicker` class — use `Fields\EthiopicDateTimePicker` instead
- Removed empty `Pages/` directory
- Removed `console.log` debug statements from Alpine.js component
- Rebuilt dist JS bundle (production-clean, no debug output)
- Display mode enum values standardized: `ethiopic_amharic`, `ethiopic_english`, `gregorian`, `dual`

### Removed
- `Mammesat\FilamentEthiopicCalendar\Forms\Components\EthiopicDatePicker` (deprecated since v1.1.0)

### Migration from v1.x

Replace all imports:

```diff
- use Mammesat\FilamentEthiopicCalendar\Forms\Components\EthiopicDatePicker;
+ use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;
```

Replace field usage:

```diff
- EthiopicDatePicker::make('date')
+ EthiopicDateTimePicker::make('date')->ethiopic()
```

## [1.1.0] - 2026-04-24

### Added
- `Support\EthiopicFormatter` — thin static facade for programmatic formatting access
- `Support\SettingsResolver` — unified config resolution (Field Override → Config → Default)
- `formatEthiopianTime()` method on `Services\EthiopicFormatter` for safe, null-aware time formatting
- `SettingsResolverTest` — full unit test suite for override priority logic
- Expanded `EthiopicFormatterTest` — Ethiopian time boundary tests, Pagume (13th month) tests, all display mode verification
- Ethiopian time system support with correct period mapping
- `withTime()` fluent method for enabling time picker per field
- `time_mode` config option (`gregorian` / `ethiopian` / `dual`)
- Time display in `EthiopicDateColumn` and `EthiopicDateEntry` when time mode is active
- PHP 8.4 support in CI matrix
- Complete configuration reference in README
- Programmatic API documentation with usage examples

### Fixed
- Configuration leakage: 3 direct `config()` calls replaced with centralized `EthiopicConfig` accessors
- Missing `use InvalidArgumentException` import in `EthiopicCalendar` service
- Unguarded `Carbon::parse()` in `EthiopicDateColumn` and `EthiopicDateEntry`
- Hardcoded absolute path in `testbench.yaml` replaced with portable relative path
- Amharic month 13 inconsistency unified
- `calendarLocale()` now validates input and clamps to `'am'` on invalid values

### Changed
- All UI components now route config resolution through `EthiopicConfig`
- Expanded test suite from ~70 to 80+ tests
- Refactored formatting to enforce single source of truth (`Services\EthiopicFormatter`)

## [1.0.0] - 2026-04-19

### Added
- Full Ethiopic calendar date picker for Filament v5
- JDN-based bidirectional Gregorian ↔ Ethiopic conversion
- 7 display modes
- `EthiopicDatePicker` form component extending Filament's `DateTimePicker`
- `EthiopicDateColumn` for Filament tables
- `EthiopicDateEntry` for Filament infolists
- Custom Alpine.js calendar UI with 13-month Ethiopian calendar grid
- Dual locale calendar popup (Amharic / English transliteration)
- Runtime display mode override
- Publishable config file
- Pagume (13th month) support with correct leap year handling
- Helper text and suffix display options
- Laravel translation files for Amharic and English
- Comprehensive unit test suite (24 tests)
- GitHub Actions CI pipeline with PHP 8.2/8.3 matrix
- MIT license
