<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Display Mode
    |--------------------------------------------------------------------------
    |
    | This configures how Ethiopic calendar values are displayed across the UX.
    |
    | Supported modes:
    | - 'amharic_no_week': መስከረም 01, 2017 (Amharic, no weekday — default)
    | - 'transliteration_no_week': Meskerem 01, 2017 (English transliteration, no weekday)
    | - 'amharic_combined': መስከረም 01, 2017 / ሰኞ (Fully localized Amharic with weekday)
    | - 'transliteration_combined': Meskerem 01, 2017 / Monday (English transliteration with weekday)
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
    | Time Display Format
    |--------------------------------------------------------------------------
    |
    | Controls how time is rendered when time support is enabled.
    |
    | Supported:
    | - 'standard': Regular Gregorian time (HH:mm)
    | - 'ethiopian': Local Ethiopian clock (day starts at 06:00 Gregorian)
    |
    */
    'time_format' => 'standard',
];
