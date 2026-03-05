<x-filament-panels::page.simple>

    {{-- Seletor de Idioma --}}
    <div class="mb-4 flex justify-end gap-2">
        @foreach(['pt_BR' => '🇧🇷 PT', 'en' => '🇺🇸 EN', 'es' => '🇪🇸 ES'] as $lang => $label)
            <form method="POST" action="{{ route('locale.switch') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ $lang }}">
                <input type="hidden" name="redirect" value="{{ url()->current() }}">
                <button
                    type="submit"
                    class="px-2 py-1 text-xs rounded border transition-colors
                           {{ app()->getLocale() === $lang
                               ? 'bg-primary-600 text-white border-primary-600'
                               : 'border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    {{ $label }}
                </button>
            </form>
        @endforeach
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}
        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

</x-filament-panels::page.simple>
