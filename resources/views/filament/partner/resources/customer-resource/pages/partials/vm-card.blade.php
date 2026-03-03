<div class="rounded-xl border border-gray-600 bg-gray-800/40 flex flex-col overflow-hidden hover:border-primary-500/50 transition-colors">

    {{-- VM Header --}}
    <div class="px-4 py-2.5 border-b border-gray-700 flex items-start justify-between gap-2">
        <div class="flex items-start gap-3 min-w-0">
            <div class="w-9 h-9 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center shrink-0 mt-0.5">
                <x-heroicon-m-computer-desktop class="w-5 h-5 text-gray-300" />
            </div>
            <div class="min-w-0">
                <p class="font-bold text-white text-sm truncate leading-tight">{{ $vm->name }}</p>
                @if($vm->osDistribution)
                    <p class="text-[11px] text-gray-400 truncate leading-tight">{{ $vm->osDistribution->name }}</p>
                @endif
                <div class="flex items-center gap-1 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $vm->status === 'active' ? 'bg-success-400' : 'bg-gray-500' }}"></span>
                    <span class="text-[10px] {{ $vm->status === 'active' ? 'text-success-400' : 'text-gray-500' }}">
                        {{ $vm->status === 'active' ? 'On' : 'Off' }}
                    </span>
                </div>
            </div>
        </div>
        {{-- Botões editar / excluir — x-on:click com $wire para garantir funcionamento --}}
        <div class="flex items-center gap-0.5 shrink-0">
            <button
                type="button"
                x-on:click="$wire.mountAction('editar_vm', { vm_id: {{ $vm->id }} })"
                class="p-1.5 rounded-lg text-gray-400 hover:text-primary-400 hover:bg-primary-900/30 transition-colors"
                title="Editar VM">
                <x-heroicon-m-pencil-square class="w-4 h-4" />
            </button>
            <button
                type="button"
                x-on:click="$wire.mountAction('excluir_vm', { vm_id: {{ $vm->id }} })"
                class="p-1.5 rounded-lg text-gray-400 hover:text-danger-400 hover:bg-danger-900/30 transition-colors"
                title="Excluir VM">
                <x-heroicon-m-trash class="w-4 h-4" />
            </button>
        </div>
    </div>

    {{-- Recursos em linha horizontal — ícone + número + label --}}
    <div class="flex items-stretch divide-x divide-gray-700 border-b border-gray-700 bg-gray-900/20">
        <div class="flex-1 px-3 py-3 flex flex-col items-center justify-center gap-0.5">
            <x-heroicon-m-cpu-chip class="w-4 h-4 text-gray-400 mb-0.5" />
            <span class="text-lg font-bold text-white leading-none">{{ $vm->cpu_cores }}</span>
            <span class="text-[10px] text-gray-400">vCPU</span>
        </div>
        <div class="flex-1 px-3 py-3 flex flex-col items-center justify-center gap-0.5">
            <x-heroicon-m-circle-stack class="w-4 h-4 text-gray-400 mb-0.5" />
            <span class="text-lg font-bold text-white leading-none">{{ $vm->ram_gb }}</span>
            <span class="text-[10px] text-gray-400">GB RAM</span>
        </div>
        <div class="flex-1 px-3 py-3 flex flex-col items-center justify-center gap-0.5">
            <x-heroicon-m-server class="w-4 h-4 text-gray-400 mb-0.5" />
            <span class="text-lg font-bold text-white leading-none">{{ $vm->disk_os_gb + $vm->additionalDisks->sum('size_gb') }}</span>
            <span class="text-[10px] text-gray-400">GB Disco</span>
        </div>
    </div>

    {{-- Detalhes extras --}}
    <div class="px-4 py-2.5 space-y-1.5 flex-1">
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-gray-500">💿 Disco SO</span>
            <span class="text-gray-300">{{ $vm->disk_os_gb }} GB · {{ $vm->diskOsType?->name }}</span>
        </div>
        @foreach($vm->additionalDisks as $d)
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-gray-500">💾 +Disco</span>
            <span class="text-gray-300">{{ $d->size_gb }} GB · {{ $d->diskType?->name }}</span>
        </div>
        @endforeach
        @if($vm->price_os_license > 0)
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-gray-500">🔑 Lic. SO</span>
            <span class="text-success-400">R$ {{ number_format($vm->price_os_license, 2, ',', '.') }}</span>
        </div>
        @endif
        @if($vm->price_rds > 0)
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-gray-500">🖥️ Terminal ({{ $vm->rds_license_qty }}x)</span>
            <span class="text-success-400">R$ {{ number_format($vm->price_rds, 2, ',', '.') }}</span>
        </div>
        @endif
        @if($vm->endpointSecurity)
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-gray-500">🛡️ Endpoint</span>
            <span class="text-success-400">R$ {{ number_format($vm->price_endpoint, 2, ',', '.') }}</span>
        </div>
        @endif
        @if($vm->has_backup)
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-gray-500">🗄️ Backup</span>
            <span class="text-success-400">R$ {{ number_format($vm->price_backup + $vm->price_backup_software, 2, ',', '.') }}</span>
        </div>
        @endif
    </div>

    {{-- Footer preço VM --}}
    <div class="px-4 py-2.5 border-t border-gray-700 bg-gray-900/40 flex items-center justify-end">
        <span class="text-base font-bold text-primary-400">
            R$ {{ number_format($vm->price_total_monthly, 2, ',', '.') }}
        </span>
    </div>
</div>
