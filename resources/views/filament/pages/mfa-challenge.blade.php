<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verificação MFA — {{ config('app.name') }}</title>
    @filamentStyles
    @livewireStyles
</head>
<body class="antialiased bg-gray-950 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        <div class="flex justify-center mb-8">
            <span class="text-white text-2xl font-bold tracking-tight">
                {{ config('app.name') }}
            </span>
        </div>

        <div class="bg-gray-900 rounded-2xl shadow-xl ring-1 ring-white/10 p-8">
            <div class="text-center mb-6">
                <div class="text-5xl mb-3">🔐</div>
                <h1 class="text-xl font-bold text-white">
                    Verificação em dois fatores
                </h1>
                <p class="mt-2 text-sm text-gray-400">
                    Abra o <strong class="text-gray-300">Google Authenticator</strong><br>
                    e digite o código de 6 dígitos
                </p>
            </div>

            <form wire:submit="verify">
                {{ $this->form }}

                <div class="mt-6">
                    <button
                        type="submit"
                        class="w-full flex justify-center items-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-colors"
                        wire:loading.attr="disabled"
                        wire:target="verify"
                    >
                        <span wire:loading.remove wire:target="verify">🔑 Verificar código</span>
                        <span wire:loading wire:target="verify">Verificando...</span>
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ filament()->getLoginUrl() }}"
                   class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                    ← Voltar para o login
                </a>
            </div>
        </div>
    </div>

    @livewire('notifications')
    @livewireScripts
    @filamentScripts
</body>
</html>
