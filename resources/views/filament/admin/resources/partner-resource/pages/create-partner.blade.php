<x-filament-panels::page>

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
                this.show   = true;
                this.copied = false;
            });
        },
        copyAndRedirect() {
            const text = 'Acesso ao Portal do Parceiro\n\nNome: ' + this.credName + '\nE-mail: ' + this.credEmail + '\nSenha: ' + this.credPassword + '\nURL: ' + this.credUrl;
            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => {
                    window.location.href = '{{ filament()->getCurrentPanel()->getUrl() }}/partners';
                }, 1500);
            });
        },
        skipAndRedirect() {
            window.location.href = '{{ filament()->getCurrentPanel()->getUrl() }}/partners';
        }
    }"
    x-cloak
>
    <div
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-black/70 z-[999] flex items-center justify-center p-4"
    >
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            @click.stop
        >
            {{-- Header --}}
            <div class="bg-gradient-to-r from-success-600 to-success-700 p-5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <x-heroicon-o-user-plus class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">Parceiro Cadastrado! 🎉</h2>
                    <p class="text-sm text-success-200">Usuário criado automaticamente</p>
                </div>
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

                <div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-3 flex gap-2">
                    <span class="text-amber-500 shrink-0">⚠️</span>
                    <p class="text-xs text-amber-700 dark:text-amber-400">
                        <strong>Atenção:</strong> Esta senha não será exibida novamente. Copie e envie ao parceiro antes de fechar.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5 flex gap-3">
                <button
                    @click="copyAndRedirect()"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200"
                    :class="copied ? 'bg-success-500 text-white' : 'bg-primary-600 hover:bg-primary-700 text-white'"
                >
                    <x-heroicon-m-clipboard-document class="w-4 h-4" />
                    <span x-text="copied ? '✅ Copiado! Redirecionando...' : '📋 Copiar e ir para lista'"></span>
                </button>
                <button
                    @click="skipAndRedirect()"
                    class="px-5 py-2.5 rounded-lg font-semibold text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-all"
                >
                    Pular
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Formulário --}}
<x-filament-panels::form wire:submit="create">
    {{ $this->form }}
    <x-filament-panels::form.actions
        :actions="$this->getCachedFormActions()"
        :full-width="$this->hasFullWidthFormActions()"
    />
</x-filament-panels::form>

</x-filament-panels::page>
