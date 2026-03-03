<x-filament-panels::page>

    @php $company = \App\Models\Company::first(); @endphp

    {{-- Header com logo atual --}}
    @if($company?->logo)
    <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-2">
        <img
            src="{{ Storage::url($company->logo) }}"
            alt="{{ $company->trade_name ?? $company->name }}"
            class="h-16 object-contain"
        />
        <div>
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">
                {{ $company->trade_name ?? $company->name }}
            </h3>
            @if($company->cnpj)
            <p class="text-sm text-gray-500">CNPJ: {{ $company->cnpj }}</p>
            @endif
            @if($company->city && $company->state)
            <p class="text-sm text-gray-500">{{ $company->city }} / {{ $company->state }}</p>
            @endif
        </div>
        <div class="ml-auto">
            <x-filament::badge :color="$company->is_active ? 'success' : 'danger'">
                {{ $company->is_active ? 'Ativa' : 'Inativa' }}
            </x-filament::badge>
        </div>
    </div>
    @endif

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-m-check" size="lg">
                Salvar dados da empresa
            </x-filament::button>
        </div>
    </form>

</x-filament-panels::page>
