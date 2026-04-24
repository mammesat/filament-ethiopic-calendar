# Changelog

All notable changes to `filament-ethiopic-calendar` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-04-24

### Added
- `Support\EthiopicFormatter` — thin static facade for programmatic formatting access
- `Support\SettingsResolver` — unified config resolution (Field Override → Config → Default)
- `formatEthiopianTime()` method on `Services\EthiopicFormatter` for safe, null-aware time formatting
- `SettingsResolverTest` — full unit test suite for override priority logic
- Expanded `EthiopicFormatterTest` — Ethiopian time boundary tests, Pagume (13th month) tests, all display mode verification
- Ethiopian time system support with correct period mapping (ጥዋት, ከሰዓት, ማታ, ለሊት)
- `withTime()` fluent method for enabling time picker per field
- `time_mode` config option (`gregorian` / `ethiopian` / `dual`)
- Time display in `EthiopicDateColumn` and `EthiopicDateEntry` when time mode is active
- PHP 8.4 support in CI matrix
- Complete configuration reference in README
- Programmatic API documentation with usage examples

### Fixed
- Configuration leakage: 3 direct `config()` calls replaced with centralized `EthiopicConfig` accessors
  - `HasEthiopicFormatting::hasTime()`
  - `EthiopicDateTimePicker::hasTime()`
  - `EthiopicDateTimePicker::getCalendarLocale()`
- Missing `use InvalidArgumentException` import in `EthiopicCalendar` service
- Unguarded `Carbon::parse()` in `EthiopicDateColumn` and `EthiopicDateEntry`
- Hardcoded absolute path in `testbench.yaml` replaced with portable relative path
- Amharic month 13 inconsistency unified to `ጳጉሜ`
- `calendarLocale()` now validates input and clamps to `'am'` on invalid values

### Changed
- All UI components now route config resolution through `EthiopicConfig`
- Expanded test suite from ~70 to 80+ tests
- Refactored formatting to enforce single source of truth (`Services\EthiopicFormatter`)

## [1.0.0] - 2026-04-19

### Added
- Full Ethiopic calendar date picker for Filament v5
- JDN-based bidirectional Gregorian ↔ Ethiopic conversion
- 7 display modes: `amharic_combined`, `transliteration_combined`, `clean_gregorian`, `hybrid`, `compact_amharic`, `amharic_no_week`, `transliteration_no_week`
- `EthiopicDatePicker` form component extending Filament's `DateTimePicker`
- `EthiopicDateColumn` for Filament tables
- `EthiopicDateEntry` for Filament infolists
- Custom Alpine.js calendar UI with 13-month Ethiopian calendar grid
- Dual locale calendar popup (Amharic / English transliteration)
- Runtime display mode override via `ethiopicDisplayMode()` / `displayMode()`
- Publishable config file with `display_mode`, `locale`, `calendar_locale` settings
- Pagume (13th month) support with correct leap year handling
- Helper text and suffix display options
- Laravel translation files for Amharic and English
- Comprehensive unit test suite (24 tests)
- GitHub Actions CI pipeline with PHP 8.2/8.3 matrix
- MIT license
