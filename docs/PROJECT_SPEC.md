# Filament Ethiopic Date Picker — Project Specification

## 📌 Project Name
filament-ethiopic-date-picker

---

# 🎯 1. Objective

Build a Composer-installable Laravel package that extends Filament v5 by introducing an Ethiopic Calendar Date Picker field.

The plugin must:
- Allow users to input dates using the Ethiopic calendar
- Convert Ethiopic dates → Gregorian before storing in database
- Convert Gregorian → Ethiopic when displaying in forms
- Be fully compatible with Filament v5 and Livewire v4
- Follow Laravel package best practices

---

# 🧱 2. Core Principles

- ❌ Do NOT extend Filament DatePicker
- ❌ Do NOT use macros or internal hacks
- ❌ Do NOT modify Filament core
- ✅ Use custom Filament Field component
- ✅ Keep conversion logic framework-independent
- ✅ Ensure clean separation of concerns

---

# 🏗️ 3. Architecture Overview

## Layers

1. Filament Field Layer
   - EthiopicDatePicker.php
   - Handles form integration

2. Conversion Service Layer
   - EthiopicCalendar.php
   - Handles all date conversions

3. UI Layer
   - Blade component
   - Alpine.js optional enhancements

4. Service Provider Layer
   - Registers plugin into Laravel + Filament

---

# 📦 4. Required Features

## Core Features (MVP)

- Ethiopic date input field
- Gregorian storage format (Y-m-d)
- Ethiopic display formatting
- Null-safe handling
- Validation support

---

## Conversion Requirements

Must support:

Methods:
- toEthiopic($year, $month, $day): array
- toGregorian($year, $month, $day): array
- toEthiopicString(?string $gregorian): ?string
- toGregorianString(?string $ethiopic): ?string

Algorithm requirements:
- Must use Julian Day Number (JDN) system
- Must correctly handle:
  - Leap years
  - Pagume (13th month)
  - Date boundaries

---

# 🎨 5. UI Requirements

## Phase 1 (MVP UI)
- Simple text input field
- Livewire binding
- Basic formatting

## Phase 2 (Improved UI)
- Year dropdown
- Month dropdown (Ethiopic months)
- Day dropdown
- Dynamic day adjustment for month length

## Phase 3 (Advanced UI)
- Calendar popup (Alpine.js)
- Month navigation
- Mobile responsive UI

---

# ⚙️ 6. Filament Field Behavior

## State Flow

Input (Ethiopic):
YYYY-MM-DD

Storage (Gregorian):
YYYY-MM-DD

Display (Ethiopic):
YYYY-MM-DD

---

## Lifecycle Hooks

Must implement:
- formatStateUsing() → convert Gregorian → Ethiopic
- dehydrateStateUsing() → convert Ethiopic → Gregorian

---

# 📁 7. Required File Structure

src/
 ├── EthiopicDatePickerServiceProvider.php
 ├── Forms/
 │    └── Components/
 │         └── EthiopicDatePicker.php
 ├── Services/
 │    └── EthiopicCalendar.php
 └── Support/

resources/
 └── views/
      └── forms/
           └── components/
                └── ethiopic-date-picker.blade.php

config/
 └── ethiopic-calendar.php

tests/
 └── (unit + integration tests)

---

# 🧪 8. Testing Requirements

## Unit Tests
- Gregorian → Ethiopic conversion accuracy
- Ethiopic → Gregorian conversion accuracy
- Leap year validation
- Pagume handling (13th month)

## Integration Tests
- Works in Filament forms
- Works in create/edit pages
- Correct database storage

---

# 📦 9. Composer Requirements

- PHP ^8.2
- Laravel compatible package
- Filament ^5.0
- PSR-4 autoloading
- Laravel package auto-discovery support

---

# 🔌 10. Service Provider Requirements

Must:
- Register Blade views
- Publish configuration file
- Auto-register with Laravel

---

# 🚀 11. Development Phases

## Phase 1 — Core Engine
- Conversion service
- Basic Filament field
- Basic UI

## Phase 2 — Structured UI
- Dropdown-based input system
- Improved UX

## Phase 3 — Calendar UI
- Popup calendar
- Alpine.js interaction

## Phase 4 — Extensibility
- Formatting customization
- Localization (Amharic/English)

## Phase 5 — Testing
- Full unit + integration coverage

## Phase 6 — Packagist Release
- Version tagging
- Documentation
- Stable release

---

# 🧠 12. Design Constraints

- Must be Laravel-native package
- Must be Filament v5 compatible
- Must not depend on internal Filament implementations
- Must be upgrade-safe for future Filament versions
- Must separate UI, logic, and conversion layers

---

# 📌 13. Expected Output from Codex

Codex must generate:

- Full Laravel package structure
- All PHP classes
- Blade components
- Service provider
- Config file
- Tests
- Composer-ready package

---

# ⚠️ 14. Final Codex Instruction

Act as a senior Laravel package maintainer.

Build a production-ready Filament v5 plugin strictly following this specification.

Ensure:
- Clean architecture
- PSR-4 compliance
- No hacks or macros
- Proper separation of concerns
- Fully Composer installable package

Output a complete working codebase.
