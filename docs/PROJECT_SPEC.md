# Filament Ethiopic Date Picker — Technical Specification

## 1. Project Overview

**Project Name:** `filament-ethiopic-date-picker`

The objective of this project is to provide a robust, Composer-installable Laravel package that extends the Filament v5 ecosystem by introducing a native Ethiopic (Ge'ez) Calendar Date Picker and associated table column components. 

The package is designed to bridge the gap between traditional Ethiopian date tracking and standard database storage by allowing users to interact entirely with an Ethiopic calendar interface while storing all underlying dates in the standard Gregorian format.

---

## 2. Architectural Principles

To ensure long-term stability and integration within the Filament ecosystem, the following constraints and principles strictly govern the codebase:

- **Independent Component Design:** The plugin implements custom Filament Form Components (`EthiopicDatePicker`) and Table Columns (`EthiopicDateColumn`) rather than attempting to extend or override the native, core `DatePicker`.
- **Framework Independence:** Core calendar math and conversion algorithms are isolated in service classes, remaining independent of Livewire or Filament constraints.
- **Strict Separation of Concerns:** Database hydration, view presentation, and Javascript interactivity maintain strict boundaries.
- **No Core Modifications:** No macros, monkey-patching, or modifications are applied to Filament's internal core files.

---

## 3. System Architecture

The package is organized into four distinct layers:

1. **Filament Field Layer:** (`Forms/Components`, `Tables/Columns`) Responsible for integrating directly with the Filament schema, managing Livewire state dehydration and hydration.
2. **Conversion Service Layer:** (`Services/EthiopicCalendar.php`) Provides the mathematical backbone for accurate conversions between identical dates in the Gregorian and Ethiopic calendars.
3. **User Interface Layer:** (`resources/views`, Alpine.js) Manages the client-side rendering of the interactive calendar popup, ensuring a responsive and accessible user experience matching Filament's native styling.
4. **Service Provider Layer:** Responsible for bootstrapping the package, publishing configurations, and registering assets within the Laravel lifecycle.

---

## 4. Feature Specifications

### 4.1 Core Requirements
- **Native Input Field:** An Ethiopic date input field seamlessly integrating into Filament forms.
- **Gregorian Storage:** Invisible translation of inputs into standard `Y-m-d` or `Y-m-d H:i:s` strings for standard SQL compatibility.
- **Null states & Validation:** Full support for `required()`, nullable fields, and standard Eloquent date validations.
- **Time Picker:** Optional hour, minute, and second selection capabilities alongside the calendar.

### 4.2 Conversion Accuracy
The internal calculation engine must guarantee exact bidirectional translation methods:
- Mathematical accuracy based on the Julian Day Number (JDN) system to ensure zero drift.
- Full handling of the 13th month (*Pagume*), correctly identifying years where it contains 5 or 6 days.
- Precise alignment with leap year cycles in both calendar systems.

---

## 5. UI & UX Standards

The package must present a modern interface indistinguishable from Filament's core components:

- **Calendar Popup:** An Alpine.js powered interactive dropdown displaying a traditional grid of days.
- **Month/Year Navigation:** Dedicated controls to jump between Ethiopic months and years effortlessly.
- **Display Modes:** Multiple supported visual representations including pure Amharic, English Transliteration, and compact formatting depending on developer configuration.
- **Mobile Responsiveness:** Touch-friendly target sizing and viewport-aware popup positioning.

---

## 6. Data Lifecycle

The lifecycle of a date within the system follows a strict mutation path:

1. **Client Input (Ethiopic):** User selects `YYYY-MM-DD` on the visual calendar. The Alpine component immediately processes this and translates it locally into a Gregorian equivalent.
2. **Dehydration (Gregorian):** The Livewire payload carries the Gregorian string. Filament's `dehydrateStateUsing()` hook ensures the underlying Eloquent model saves `YYYY-MM-DD` gracefully.
3. **Hydration/Display (Ethiopic):** Upon rendering existing data, the `formatStateUsing()` hook intercepts the Gregorian database string and converts it back into an Ethiopic representation for the user to read.

---

## 7. Quality Assurance (Testing Requirements)

Stability is ensured through comprehensive automated testing:

- **Unit Testing:** Validating the accuracy of the `EthiopicCalendar` conversion service against known constant dates. Specific tests must target edge cases such as leap years and *Pagume*.
- **Integration Testing:** Ensuring the components successfully register in a Testbench environment, correctly intercept Livewire state updates, and trigger appropriate translations without fatal errors.

---

## 8. Development Stack

- **Language:** PHP `^8.2`
- **Framework:** Laravel `^11.0 | ^12.0 | ^13.0`
- **TALL Stack Integration:** Filament `^5.0`
- **Module Support:** Fully PSR-4 compliant and utilizing standard Laravel package auto-discovery mechanisms.
