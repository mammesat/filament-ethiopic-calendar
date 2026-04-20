<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->getForm('form') }}

        <div class="flex flex-wrap items-center gap-4 justify-start">
            <x-filament::button type="submit" size="sm">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
