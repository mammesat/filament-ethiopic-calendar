@php
    use Filament\Support\Facades\FilamentView;
    use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;

    $datalistOptions = $getDatalistOptions();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $hasTime = $hasTime();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $maxDate = $getMaxDate();
    $minDate = $getMinDate();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
    $calendarService = app(\Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendar::class);

    // Calendar UI locale determines month/day names in the popup
    $calendarLocale = $getCalendarLocale();
    $calendarUiMode = $calendarLocale === 'en'
        ? DisplayMode::TransliterationCombined
        : DisplayMode::AmharicCombined;

    $months = collect(range(1, 13))
        ->map(fn ($i) => $calendarService->getDisplayMonthName($i, $calendarUiMode))
        ->toArray();

    $dayLabels = collect(range(0, 6))
        ->mapWithKeys(fn ($i) => [
            $i => $calendarLocale === 'en'
                ? strtoupper(substr((string) $calendarService->getDisplayDayName($i, $calendarUiMode), 0, 3))
                : $calendarService->getDisplayDayName($i, $calendarUiMode)
        ])
        ->toArray();

    // Short labels: use compact mode matching the calendar locale
    $dayShortMode = $calendarLocale === 'en'
        ? DisplayMode::TransliterationCombined
        : DisplayMode::CompactAmharic;

    $dayShortLabels = collect(range(0, 6))
        ->mapWithKeys(fn ($i) => [
            $i => $calendarLocale === 'en'
                ? strtoupper(substr((string) $calendarService->getDisplayDayName($i, $dayShortMode), 0, 3))
                : $calendarService->getDisplayDayName($i, $dayShortMode)
        ])
        ->toArray();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$getPrefixIconColor()"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$getSuffixIconColor()"
        :valid="! $errors->has($statePath)"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
            <div
                @if (FilamentView::hasSpaMode())
                    {{-- format-ignore-start --}}x-load="visible || event (ax-modal-opened)"{{-- format-ignore-end --}}
                @else
                    x-load
                @endif
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-ethiopic-calendar', 'mammesat/filament-ethiopic-calendar') }}"
                x-data="filamentEthiopicCalendarComponent({
                            displayFormat:
                                '{{ convert_date_format($getDisplayFormat())->to('day.js') }}',
                            firstDayOfWeek: {{ $getFirstDayOfWeek() }},
                            isAutofocused: @js($isAutofocused()),
                            locale: @js($getLocale()),
                            shouldCloseOnDateSelection: @js($shouldCloseOnDateSelection()),
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                            months: @js($months),
                            dayLabel: @js($dayLabels),
                            dayShortLabel: @js($dayShortLabels)
                        })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{
                    $attributes
                        ->merge($getExtraAttributes(), escape: false)
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class(['fi-fo-date-time-picker'])
                }}
            >
                <input x-ref="maxDate" type="hidden" value="{{ $maxDate }}" />

                <input x-ref="minDate" type="hidden" value="{{ $minDate }}" />

                <input
                    x-ref="disabledDates"
                    type="hidden"
                    value="{{ json_encode($getDisabledDates()) }}"
                />

                <button
                    x-ref="button"
                    x-on:click="togglePanelVisibility()"
                    x-on:keydown.enter.stop.prevent="
                        if (! $el.disabled) {
                            isOpen() ? selectDate() : togglePanelVisibility()
                        }
                    "
                    x-on:keydown.arrow-left.stop.prevent="if (! $el.disabled) focusNextDay()"
                    x-on:keydown.arrow-right.stop.prevent="if (! $el.disabled) focusPreviousDay()"
                    x-on:keydown.arrow-up.stop.prevent="if (! $el.disabled) focusPreviousWeek()"
                    x-on:keydown.arrow-down.stop.prevent="if (! $el.disabled) focusNextWeek()"
                    x-on:keydown.backspace.stop.prevent="if (! $el.disabled) clearState()"
                    x-on:keydown.clear.stop.prevent="if (! $el.disabled) clearState()"
                    x-on:keydown.delete.stop.prevent="if (! $el.disabled) clearState()"
                    aria-label="{{ $getPlaceholder() }}"
                    type="button"
                    tabindex="-1"
                    @disabled($isDisabled || $isReadOnly())
                    {{
                        $getExtraTriggerAttributeBag()->class([
                            'fi-fo-date-time-picker-trigger',
                        ])
                    }}
                >
                    <input
                        @disabled($isDisabled)
                        readonly
                        placeholder="{{ $getPlaceholder() }}"
                        wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.display-text"
                        x-model="displayText"
                        dir="ltr"
                        @if ($id = $getId()) id="{{ $id }}" @endif
                        @class([
                            'fi-fo-date-time-picker-display-text-input',
                        ])
                    />
                </button>

                <div
                    x-ref="panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore
                    wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.panel"
                    @class([
                        'fi-fo-date-time-picker-panel',
                    ])
                >
                        @if ($hasDate())
                        <div class="fi-fo-date-time-picker-panel-header">
                            <select
                                x-model="focusedMonth"
                                class="fi-fo-date-time-picker-month-select"
                            >
                                <template x-for="(month, index) in months">
                                    <option
                                        x-bind:value="index"
                                        x-text="month"
                                    ></option>
                                </template>
                            </select>

                            <input
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="focusedYear"
                                class="fi-fo-date-time-picker-year-input"
                            />
                        </div>

                        <div class="fi-fo-date-time-picker-calendar-header">
                            <template
                                x-for="(day, index) in dayLabels"
                                x-bind:key="index"
                            >
                                <div
                                    x-text="day"
                                    class="fi-fo-date-time-picker-calendar-header-day"
                                ></div>
                            </template>
                        </div>

                        <div
                            role="grid"
                            class="fi-fo-date-time-picker-calendar"
                        >
                            <template
                                x-for="day in emptyDaysInFocusedMonth"
                                x-bind:key="day"
                            >
                                <div></div>
                            </template>

                            <template
                                x-for="day in daysInFocusedMonth"
                                x-bind:key="day"
                            >
                                <div
                                    x-text="day"
                                    x-on:click="dayIsDisabled(day) || selectDate(day)"
                                    x-on:mouseenter="setFocusedDay(day)"
                                    role="option"
                                    x-bind:aria-selected="focusedDate.date() === day"
                                    x-bind:class="{
                                        'fi-fo-date-time-picker-calendar-day-today': dayIsToday(day),
                                        'fi-focused': focusedDate.date() === day,
                                        'fi-selected': dayIsSelected(day),
                                        'fi-disabled': dayIsDisabled(day),
                                    }"
                                    class="fi-fo-date-time-picker-calendar-day hover:bg-gray-50 dark:hover:bg-white/5 transition duration-75 cursor-pointer"
                                ></div>
                            </template>
                        </div>
                        @endif

                        @if ($hasTime)
                        <div class="fi-fo-date-time-picker-time-inputs">
                            <input
                                max="23"
                                min="0"
                                step="{{ $getHoursStep() }}"
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="hour"
                            />

                            <span
                                class="fi-fo-date-time-picker-time-input-separator"
                            >
                                :
                            </span>

                            <input
                                max="59"
                                min="0"
                                step="{{ $getMinutesStep() }}"
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="minute"
                            />

                            @if ($hasSeconds())
                                <span
                                    class="fi-fo-date-time-picker-time-input-separator"
                                >
                                    :
                                </span>

                                <input
                                    max="59"
                                    min="0"
                                    step="{{ $getSecondsStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="second"
                                />
                            @endif
                        </div>
                        @endif
                </div>
            </div>
    </x-filament::input.wrapper>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
