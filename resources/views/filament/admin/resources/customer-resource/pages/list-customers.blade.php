<x-filament-panels::page>

{{-- Seletor de Parceiro --}}
<div class="mb-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 shrink-0">
            <x-heroicon-o-briefcase class="w-4 h-4 text-primary-400"/>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">Parceiro:</span>
        </div>
        <div class="flex-1 max-w-xs relative">
            <select
                wire:model.live="selectedPartnerId"
                class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none appearance-none cursor-pointer border bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
            >
                <option value="">Selecione um parceiro...</option>
                @foreach($this->getPartners() as $partner)
                    <option value="{{ $partner->id }}" @selected($this->selectedPartnerId == $partner->id)>
                        {{ $partner->trade_name ?? $partner->company_name }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                <x-heroicon-m-chevron-down class="w-4 h-4 text-gray-400"/>
            </div>
        </div>
        @if($this->selectedPartnerId)
            <button wire:click="$set('selectedPartnerId', null)" class="text-xs text-gray-500 hover:text-white transition-colors">
                Limpar
            </button>
        @endif
    </div>
</div>

{{ $this->table }}

<x-filament-actions::modals />
</x-filament-panels::page>
