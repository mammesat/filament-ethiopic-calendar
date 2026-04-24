<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Display Mode
    |--------------------------------------------------------------------------
    |
    | Controls how Ethiopic calendar values are displayed across the UX.
    |
    | Supported modes:
    | - 'amharic_no_week': መስከረም 01, 2017 (Amharic, no weekday — default)
    | - 'transliteration_no_week': Meskerem 01, 2017 (English transliteration, no weekday)
    | - 'amharic_combined': መስከረም 01, 2017 / ሰኞ (Fully localized Amharic with weekday)
    | - 'transliteration_combined': Meskerem 01, 2017 / Monday (English with weekday)
    | - 'clean_gregorian': 2024-09-11 (No Ethiopic labels, pure Gregorian format)
    | - 'hybrid': Meskerem (መስከረም) 01, 2017 / Monday (ሰኞ) (Bilingual display)
    | - 'compact_amharic': መስከረም 01, 2017 ሰኞ (Amharic only, compact spacing)
    |
    */
    'display_mode' => 'amharic_no_week',

    /*
    |--------------------------------------------------------------------------
    | Locale
    |--------------------------------------------------------------------------
    |
    | Lightweight locale fallback used when resolving display mode.
    | If 'display_mode' is explicitly set, it always takes precedence.
    |
    | Supported: 'am' (Amharic), 'en' (English transliteration), 'hybrid'
    |
    */
    'locale' => 'am',

    /*
    |--------------------------------------------------------------------------
    | Calendar UI Locale
    |--------------------------------------------------------------------------
    |
    | Controls the language of month and day names shown in the calendar popup.
    |
    | - 'am': Amharic (መስከረም, ሰኞ, etc.)
    | - 'en': English transliteration (Meskerem, Monday, etc.)
    |
    */
    'calendar_locale' => 'am',

    /*
    |--------------------------------------------------------------------------
    | Time Picker
    |--------------------------------------------------------------------------
    |
    | Enable or disable the time picker globally.
    | When disabled (default), only date selection is available.
    | When enabled, the picker shows HH:mm input fields and stores datetime.
    |
    */
    'with_time' => false,

    /*
    |--------------------------------------------------------------------------
    | Time Mode
    |--------------------------------------------------------------------------
    |
    | Controls which time system the application operates in.
    |
    | Supported:
    | - 'gregorian':  Standard AM/PM — no Ethiopian transformation (default)
    | - 'ethiopian':  Ethiopian hour system (1–12 starting at 6:00 Gregorian)
    |                 with day periods (ጠዋት, ከሰዓት, ማታ, ለሊት)
    | - 'dual':       Both Gregorian and Ethiopian displayed together
    |                 e.g. "10:00 AM (ጠዋት 4:00)"
    |
    */
    'time_mode' => 'gregorian',

    /*
    |--------------------------------------------------------------------------
    | Dual Time Display Format
    |--------------------------------------------------------------------------
    |
    | Template for dual time mode display.
    | Available placeholders: :gregorian, :ethiopian
    |
    | Examples:
    |   ':gregorian (:ethiopian)'   → "10:00 AM (ጠዋት 4:00)"
    |   ':ethiopian (:gregorian)'   → "ጠዋት 4:00 (10:00 AM)"
    |
    */
    'dual_time_format' => ':gregorian (:ethiopian)',

    /*
    |--------------------------------------------------------------------------
    | Time Display Format (DEPRECATED — use 'time_mode' instead)
    |--------------------------------------------------------------------------
    |
    | Legacy key kept for backward compatibility. Ignored if 'time_mode' is set.
    | Maps: 'standard' → 'gregorian', 'ethiopian' → 'ethiopian'
    |
    */
    // 'time_format' => 'standard',

    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | Default timezone for Ethiopian date/time operations.
    |
    */
    'timezone' => 'Africa/Addis_Ababa',
];
