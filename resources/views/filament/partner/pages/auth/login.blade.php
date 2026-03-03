<x-filament-panels::page.simple>
    {{-- Seletor de idioma --}}
    <div class="flex justify-center gap-2 mb-4">
        @foreach(['pt_BR' => '🇧🇷 Português', 'en' => '🇺🇸 English', 'es' => '🇦🇷 Español'] as $loc => $label)
        <button type="button"
            wire:click="$set('locale', '{{ $loc }}')"
            class="px-3 py-1 text-xs rounded-full border transition-colors {{ $locale === $loc ? 'border-primary-500 bg-primary-900/30 text-primary-400' : 'border-gray-700 text-gray-500 hover:text-white' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>
