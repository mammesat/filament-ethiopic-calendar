@php
    $statePath = $getStatePath();
    $months = config('ethiopic-calendar.months', []);
    $yearOptions = $getYearOptions();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: @js($getState()),
            year: null,
            month: null,
            day: null,
            isHydrating: false,
            init() {
                this.hydrateFromState(this.state);

                this.$watch('state', value => {
                    if (this.isHydrating) {
                        return;
                    }

                    const normalized = this.normalizeState(value);

                    if (normalized === this.composeState()) {
                        return;
                    }

                    this.hydrateFromState(normalized);
                });

                this.$watch('year', () => this.syncState());
                this.$watch('month', () => this.syncState());
                this.$watch('day', () => this.syncState());
            },
            normalizeState(value) {
                if (typeof value !== 'string' || ! /^\d{4}-\d{2}-\d{2}$/.test(value)) {
                    return null;
                }

                return value;
            },
            hydrateFromState(value) {
                this.isHydrating = true;

                const normalized = this.normalizeState(value);

                if (normalized === null) {
                    this.year = null;
                    this.month = null;
                    this.day = null;
                    this.state = null;
                    this.isHydrating = false;

                    return;
                }

                const [year, month, day] = normalized.split('-').map(part => parseInt(part, 10));
                this.year = Number.isInteger(year) ? year : null;
                this.month = Number.isInteger(month) ? month : null;
                this.day = Number.isInteger(day) ? day : null;

                const maxDays = this.daysInMonth();

                if (this.day !== null && this.day > maxDays) {
                    this.day = maxDays;
                }

                this.state = this.composeState();
                this.isHydrating = false;
            },
            isLeapYear() {
                return this.year !== null && ((this.year + 1) % 4) === 0;
            },
            daysInMonth() {
                if (this.month === null) {
                    return 30;
                }

                if (this.month <= 12) {
                    return 30;
                }

                return this.isLeapYear() ? 6 : 5;
            },
            dayOptions() {
                return Array.from({ length: this.daysInMonth() }, (_, index) => index + 1);
            },
            composeState() {
                if (this.year === null || this.month === null || this.day === null) {
                    return null;
                }

                return [
                    this.year.toString().padStart(4, '0'),
                    this.month.toString().padStart(2, '0'),
                    this.day.toString().padStart(2, '0'),
                ].join('-');
            },
            syncState() {
                if (this.isHydrating) {
                    return;
                }

                if (this.day !== null && this.day > this.daysInMonth()) {
                    this.day = this.daysInMonth();
                }

                this.state = this.composeState();
            },
        }"
        class="grid grid-cols-1 gap-3 md:grid-cols-3"
    >
        <input
            type="hidden"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
            x-model="state"
        />

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Year</span>
            <select
                x-model.number="year"
                @disabled($isDisabled())
                class="fi-select-input block w-full rounded-lg border-none bg-white/0 px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:ring-white/10 dark:focus:ring-primary-500"
            >
                <option value="">Select year</option>
                @foreach ($yearOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Month</span>
            <select
                x-model.number="month"
                @disabled($isDisabled())
                class="fi-select-input block w-full rounded-lg border-none bg-white/0 px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:ring-white/10 dark:focus:ring-primary-500"
            >
                <option value="">Select month</option>
                @foreach ($months as $value => $label)
                    <option value="{{ $value }}">{{ str_pad((string) $value, 2, '0', STR_PAD_LEFT) }} - {{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Day</span>
            <select
                x-model.number="day"
                @disabled($isDisabled())
                class="fi-select-input block w-full rounded-lg border-none bg-white/0 px-3 py-2 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:ring-white/10 dark:focus:ring-primary-500"
            >
                <option value="">Select day</option>
                <template x-for="dayOption in dayOptions()" :key="dayOption">
                    <option :value="dayOption" x-text="dayOption.toString().padStart(2, '0')"></option>
                </template>
            </select>
        </label>
    </div>
</x-dynamic-component>
