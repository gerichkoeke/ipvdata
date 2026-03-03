<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna esquerda: Dados + Senha --}}
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit="saveProfile">
                {{ $this->profileForm }}
                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit" color="primary">
                        Salvar alterações
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Coluna direita: MFA --}}
        <div class="space-y-4">
            {{ $this->mfaForm }}

            @php $user = auth()->user(); @endphp

            @if($user->mfa_enabled && $user->mfa_confirmed_at)
                <x-filament::button
                    wire:click="disableMfa"
                    color="danger"
                    wire:confirm="Tem certeza que deseja desativar o MFA?"
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
                    ✅ Confirmar e Ativar MFA
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
