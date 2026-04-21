# Changelog

All notable changes to `filament-ethiopic-calendar` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-04-21

### Added
- Ethiopian time system support with correct period mapping (ጥዋት, ከሰዓት, ማታ, ለሊት)
- `withTime()` fluent method for enabling time picker per field
- `time_format` config option (`standard` / `ethiopian`)
- Time display in `EthiopicDateColumn` and `EthiopicDateEntry` when Ethiopian time mode is active
- `formatEthiopianTime()` method on `EthiopicCalendar` service
- PHP 8.4 support in CI matrix
- `CHANGELOG.md` for release tracking

### Fixed
- Missing `use InvalidArgumentException` import in `EthiopicCalendar` service (would cause `Class not found` errors on invalid date input)
- Unguarded `Carbon::parse()` in `EthiopicDateColumn` — malformed database values no longer crash entire table rendering
- Unguarded `Carbon::parse()` in `EthiopicDateEntry` — same fix for infolist rendering
- Hardcoded absolute path in `testbench.yaml` replaced with portable relative path
- Amharic month 13 inconsistency between lang file (`ጳጉሜን`) and service (`ጳጉሜ`) — unified to `ጳጉሜ`
- `calendarLocale()` now validates input and clamps to `'am'` on invalid values
- Removed redundant `strtotime()` check in `formatEthiopianTime()` (regex is the authoritative validator)

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
