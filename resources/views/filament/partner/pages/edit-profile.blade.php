<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Dados pessoais (formulário base do Filament) --}}
        <div class="lg:col-span-2">
            <x-filament-panels::form wire:submit="save">
                {{ $this->form }}
                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="false"
                />
            </x-filament-panels::form>
        </div>

        {{-- MFA --}}
        <div class="space-y-4">
            {{ $this->mfaForm }}

            @php $user = auth()->user(); @endphp

            @if($user->mfa_enabled && $user->mfa_confirmed_at)
                <x-filament::button
                    wire:click="disableMfa"
                    color="danger"
                    wire:confirm="Desativar MFA? Sua conta ficará menos segura."
                    class="w-full justify-center"
                >
                    🔓 Desativar MFA
                </x-filament::button>
            @elseif($mfaQrCode)
                <x-filament::button
                    wire:click="confirmMfa"
                    color="success"
                    class="w-full justify-center"
                >
                    ✅ Confirmar e Ativar
                </x-filament::button>
            @else
                <x-filament::button
                    wire:click="setupMfa"
                    color="warning"
                    class="w-full justify-center"
                >
                    🔐 Configurar MFA
                </x-filament::button>
            @endif
        </div>
    </div>
</x-filament-panels::page>
