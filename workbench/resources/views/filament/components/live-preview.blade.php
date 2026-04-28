@props([
    'activeMode' => 'ethiopic',
    'activeTimeMode' => 'gregorian',
    'locale' => 'am',
    'withTime' => true,
    'lines' => [],
    'activeLine' => null,
])

@php
    $datePreview = $withTime ? '2026-04-21 10:00:00' : '2026-04-21';

    $modeLabels = [
        'gregorian' => 'Gregorian',
        'ethiopic' => 'Ethiopian',
        'dual' => 'Dual (Ethiopian + Gregorian)',
    ];

    $timeLabels = [
        'gregorian' => 'Gregorian',
        'ethiopian' => 'Ethiopian',
        'dual' => 'Dual',
    ];

    $dualOutput = $lines['dual'] ?? null;

    if ($withTime && is_string($dualOutput) && str_contains($dualOutput, ') ')) {
        $dualOutput = preg_replace('/\)\s+/', ")\n", $dualOutput, 1);
    }
@endphp

<div class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="space-y-1">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Live Preview</p>
        <p class="text-sm text-gray-600 dark:text-gray-300">Showing output for <span class="font-medium">{{ $datePreview }}</span></p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Active mode: <span class="font-medium">{{ $modeLabels[$activeMode] ?? ucfirst($activeMode) }}</span>
            • Time mode: <span class="font-medium">{{ $timeLabels[$activeTimeMode] ?? ucfirst($activeTimeMode) }}</span>
            • Locale: <span class="font-medium">{{ $locale === 'am' ? 'Ethiopian (Amharic)' : 'Ethiopian (English)' }}</span>
        </p>
    </div>

    <div class="rounded-lg border border-primary-200 bg-primary-50/70 p-3 text-sm dark:border-primary-700/60 dark:bg-primary-900/30">
        <p class="text-xs font-semibold uppercase tracking-wide text-primary-700 dark:text-primary-200">Current Selection</p>
        <p class="mt-1 whitespace-pre-line text-base font-semibold text-primary-700 dark:text-primary-100">{{ $activeLine }}</p>
    </div>

    <div class="grid gap-3 md:grid-cols-3">
        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Gregorian</p>
            <p class="mt-2 whitespace-pre-line text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lines['gregorian'] ?? '—' }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ethiopian</p>
            <p class="mt-2 whitespace-pre-line text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lines['ethiopian'] ?? '—' }}</p>
        </div>

        <div class="rounded-lg border border-primary-200 bg-primary-50/60 p-3 dark:border-primary-700/60 dark:bg-primary-900/30">
            <p class="text-xs font-bold uppercase tracking-wide text-primary-700 dark:text-primary-100">Dual <span class="font-medium">(Recommended)</span></p>
            <p class="mt-2 whitespace-pre-line text-sm font-semibold text-primary-700 dark:text-primary-100">{{ $dualOutput ?? '—' }}</p>
        </div>
    </div>
</div>
