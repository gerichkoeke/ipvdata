<x-filament-panels::page>

@php
    $partner = $this->record;
    $partnerUser = \App\Models\User::where('partner_id', $partner->id)
        ->orWhere(fn($q) => $q->where('email', $partner->email)->where('panel', 'partner'))
        ->first();
@endphp

{{-- Banner de status do usuário --}}
@if(!$partnerUser)
<div class="rounded-xl border border-warning-300 bg-warning-50 dark:bg-warning-950 dark:border-warning-700 p-4 flex items-start gap-3 mb-4">
    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-warning-500 mt-0.5 shrink-0" />
    <div>
        <p class="font-semibold text-warning-700 dark:text-warning-400">Este parceiro ainda não possui usuário de acesso</p>
        <p class="text-sm text-warning-600 dark:text-warning-500 mt-1">
            Clique em <strong>"👤 Criar usuário de acesso"</strong> no topo da página.
        </p>
    </div>
</div>
@else
<div class="rounded-xl border border-success-300 bg-success-50 dark:bg-success-950 dark:border-success-700 p-4 flex items-start gap-3 mb-4">
    <x-heroicon-o-check-circle class="w-6 h-6 text-success-500 mt-0.5 shrink-0" />
    <div>
        <p class="font-semibold text-success-700 dark:text-success-400">Usuário de acesso configurado</p>
        <p class="text-sm text-success-600 dark:text-success-500 mt-1">
            <strong>{{ $partnerUser->name }}</strong> — {{ $partnerUser->email }}
            @if($partnerUser->is_active)
                <x-filament::badge color="success" class="ml-2">Ativo</x-filament::badge>
            @else
                <x-filament::badge color="danger" class="ml-2">Inativo</x-filament::badge>
            @endif
        </p>
    </div>
</div>
@endif

{{-- Modal Alpine.js para credenciais --}}
<div
    x-data="{
        show: false,
        credName: '',
        credEmail: '',
        credPassword: '',
        credUrl: '',
        copied: false,
        init() {
            Livewire.on('partner-user-created', (params) => {
                console.log('Evento recebido:', params);
                const data = Array.isArray(params) ? params[0] : params;
                this.credName     = data.name     || '';
                this.credEmail    = data.email    || '';
                this.credPassword = data.password || '';
                this.credUrl      = data.url      || '';
                this.show  = true;
                this.copied = false;
            });
        },
        copyText() {
            const text = 'Acesso ao Portal do Parceiro\n\nNome: ' + this.credName + '\nE-mail: ' + this.credEmail + '\nSenha: ' + this.credPassword + '\nURL: ' + this.credUrl;
            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 3000);
            });
        }
    }"
    x-cloak
>
    {{-- Overlay --}}
    <div
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-black/70 z-[999] flex items-center justify-center p-4"
    >
        {{-- Card --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            @click.stop
        >
            {{-- Header --}}
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <x-heroicon-o-key class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">Credenciais de Acesso</h2>
                    <p class="text-sm text-primary-200">Copie e envie ao parceiro com segurança</p>
                </div>
                <button @click="show = false" class="ml-auto text-white/70 hover:text-white">
                    <x-heroicon-m-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Corpo --}}
            <div class="p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 rounded-lg bg-gray-50 dark:bg-gray-800 p-3">
                        <p class="text-xs font-medium text-gray-400 mb-1">👤 Nome</p>
                        <p class="font-semibold text-gray-900 dark:text-white" x-text="credName"></p>
                    </div>
                    <div class="col-span-2 rounded-lg bg-gray-50 dark:bg-gray-800 p-3">
                        <p class="text-xs font-medium text-gray-400 mb-1">📧 E-mail</p>
                        <p class="font-semibold text-gray-900 dark:text-white" x-text="credEmail"></p>
                    </div>
                    <div class="col-span-2 rounded-lg bg-primary-50 dark:bg-primary-950 border-2 border-primary-300 dark:border-primary-700 p-3">
                        <p class="text-xs font-medium text-primary-500 mb-1">🔑 Senha gerada</p>
                        <p class="font-bold text-primary-700 dark:text-primary-300 font-mono text-xl tracking-widest" x-text="credPassword"></p>
                    </div>
                    <div class="col-span-2 rounded-lg bg-gray-50 dark:bg-gray-800 p-3">
                        <p class="text-xs font-medium text-gray-400 mb-1">🌐 URL de acesso</p>
                        <p class="font-medium text-gray-700 dark:text-gray-300 text-sm break-all" x-text="credUrl"></p>
                    </div>
                </div>

                {{-- Aviso --}}
                <div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-3 flex gap-2">
                    <span class="text-amber-500 shrink-0">⚠️</span>
                    <p class="text-xs text-amber-700 dark:text-amber-400">
                        <strong>Atenção:</strong> Esta senha não será exibida novamente. Copie agora e envie ao parceiro com segurança.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5 flex gap-3">
                <button
                    @click="copyText()"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200"
                    :class="copied ? 'bg-success-500 text-white' : 'bg-primary-600 hover:bg-primary-700 text-white'"
                >
                    <x-heroicon-m-clipboard-document class="w-4 h-4" />
                    <span x-text="copied ? '✅ Copiado!' : '📋 Copiar credenciais'"></span>
                </button>
                <button
                    @click="show = false"
                    class="px-5 py-2.5 rounded-lg font-semibold text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-all"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Formulário do Filament --}}
<x-filament-panels::form wire:submit="save">
    {{ $this->form }}
    <x-filament-panels::form.actions
        :actions="$this->getCachedFormActions()"
        :full-width="$this->hasFullWidthFormActions()"
    />
</x-filament-panels::form>

</x-filament-panels::page>
