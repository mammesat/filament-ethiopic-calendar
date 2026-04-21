@props([
    'displayMode' => 'gregorian',
    'timeMode'    => 'gregorian',
    'withTime'    => false,
])

@php
    $formatter = app(\Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter::class);
    $date = '2026-04-21 10:00:00';
    
    if (! $withTime) {
        $date = '2026-04-21';
    }

    $result = $formatter->formatDateTime($date, $displayMode, $timeMode);

    $modeLabels = [
        'gregorian' => 'Gregorian',
        'ethiopic'  => 'Ethiopic',
        'dual'      => 'Dual'
    ];
@endphp

<div class="px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
    <div class="text-sm font-medium text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">
        Preview Output
    </div>
    
    <div class="space-y-4">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                📅 Calendar: {{ $modeLabels[$displayMode] ?? $displayMode }}  &nbsp;&bull;&nbsp;  🕒 Time: {{ $modeLabels[$timeMode] ?? $timeMode }}
            </div>
            <div class="text-lg font-semibold text-primary-600 dark:text-primary-400 break-words leading-tight">
                {{ $result }}
            </div>
        </div>
    </div>
</div>
