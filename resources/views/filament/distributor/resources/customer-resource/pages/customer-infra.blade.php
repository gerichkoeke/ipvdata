<x-filament-panels::page>
@php
    $data = $this->getInfraData();
    $bp   = 'inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg';
    $rate = ($sim_currency !== 'BRL') ? max(0.01, $sim_exchange_rate) : 1.0;
    $sym  = match($sim_currency) {
        'USD' => 'US$', 'EUR' => '€', 'PYG' => '₲', 'ARS' => '$', default => 'R$'
    };
@endphp

{{-- Sub-navbar --}}
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
        <a href="{{ \App\Filament\Distributor\Resources\CustomerResource\Pages\CustomerDashboard::getUrl(['record'=>$this->record]) }}"
           class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-white transition-colors">
            <x-heroicon-m-chevron-left class="w-4 h-4"/>Dashboard
        </a>
        <span class="text-gray-700">/</span>
        <span class="text-sm font-bold text-white flex items-center gap-2">
            <x-heroicon-o-server-stack class="w-4 h-4 text-primary-400"/>Centro de Dados Virtual
        </span>
    </div>
    {{-- Distribuidor: somente leitura, sem botões de ação --}}
    <div class="flex items-center gap-3">
        <span class="text-xs text-gray-500 italic flex items-center gap-1">
            <x-heroicon-m-eye class="w-4 h-4"/> Visualização somente leitura
        </span>
        {{-- Simulação de Moeda --}}
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-700 bg-gray-800/60">
            <x-heroicon-m-currency-dollar class="w-3.5 h-3.5 text-gray-400 shrink-0"/>
            <span class="text-[10px] text-gray-400 uppercase tracking-wide">Moeda</span>
            <select wire:model.live="sim_currency"
                class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded px-1.5 py-0.5 focus:outline-none">
                <option value="BRL">BRL (R$)</option>
                <option value="USD">USD (US$)</option>
                <option value="EUR">EUR (€)</option>
                <option value="PYG">PYG (₲)</option>
                <option value="ARS">ARS ($)</option>
            </select>
            @if($sim_currency !== 'BRL')
            <span class="text-[10px] text-gray-400">1 {{ $sim_currency }} =</span>
            <input type="number" wire:model.live="sim_exchange_rate" min="0.01" step="0.01"
                class="w-20 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded px-1.5 py-0.5 focus:outline-none"
                placeholder="Taxa"/>
            <span class="text-[10px] text-gray-400">BRL</span>
            @endif
        </div>
    </div>
</div>

{{-- Stats --}}
<div style="display:flex;gap:0.75rem;" class="mb-5">
    @foreach([
        ['VMs Ativas',   $data['vms_ativas'],                                              'heroicon-o-server-stack', 'text-primary-400'],
        ['vCPUs',        $data['vcpu_total'],                                              'heroicon-o-cpu-chip',     'text-gray-300'],
        ['RAM Total',    $data['ram_total'].' GB',                                         'heroicon-o-circle-stack', 'text-gray-300'],
        ['Receita/Mês',  $sym.' '.number_format($data['mrr_total'] / $rate, 2, ',', '.'),  'heroicon-o-currency-dollar','text-success-400'],
    ] as $s)
    <div class="rounded-xl border border-gray-700 bg-gray-900 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-800 border border-gray-700 flex items-center justify-center shrink-0">
            <x-dynamic-component :component="$s[2]" class="w-4 h-4 {{ $s[3] }}"/>
        </div>
        <div>
            <p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ $s[0] }}</p>
            <p class="text-sm font-bold {{ $s[3] }}">{{ $s[1] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Rede --}}
@if($data['rede'])
@php
    $rede   = $data['rede'];
    $isLan  = $rede->networkType?->slug === 'lan-to-lan';
    $netCost = $isLan
        ? ($rede->networkType?->price ?? 0)
        : (($rede->extra_ip_price ?? 0) + ($rede->bandwidthOption?->price ?? 0));
@endphp
<div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden mb-5">
    <div class="px-5 py-3 border-b border-gray-700 bg-gray-800/60 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <x-heroicon-m-signal class="w-4 h-4 text-primary-400"/>
            <span class="font-bold text-white text-sm">Rede do Cliente</span>
            <span class="text-[10px] text-gray-500 bg-gray-800 border border-gray-700 px-2 py-0.5 rounded-full">
                Compartilhada entre todas as VMs
            </span>
        </div>
    </div>
    <div class="flex items-stretch divide-x divide-gray-800">
        <div class="flex-1 py-4 flex flex-col items-center gap-1">
            <x-heroicon-m-globe-alt class="w-5 h-5 text-primary-400 mb-0.5"/>
            <span class="text-sm font-bold text-white">{{ $rede->networkType?->name ?? '—' }}</span>
            <span class="text-[10px] text-gray-500">Tipo de Rede</span>
        </div>
        <div class="flex-1 py-4 flex flex-col items-center gap-1">
            <x-heroicon-m-wifi class="w-5 h-5 text-primary-400 mb-0.5"/>
            <span class="text-sm font-bold text-white">{{ $isLan ? '1 Gbps' : ($rede->bandwidthOption?->name ?? '—') }}</span>
            <span class="text-[10px] text-gray-500">Banda</span>
        </div>
        <div class="flex-1 py-4 flex flex-col items-center gap-1">
            <x-heroicon-m-map-pin class="w-5 h-5 text-primary-400 mb-0.5"/>
            <span class="text-sm font-bold text-white">{{ $isLan ? '—' : (1 + ($rede->extra_public_ips ?? 0)) }}</span>
            <span class="text-[10px] text-gray-500">{{ $isLan ? 'Topologia' : 'IP(s) Público(s)' }}</span>
        </div>
        <div class="flex-1 py-4 flex flex-col items-center gap-1">
            <x-heroicon-m-currency-dollar class="w-5 h-5 text-success-400 mb-0.5"/>
            <span class="text-sm font-bold text-success-400">{{ $sym }} {{ number_format($netCost / $rate, 2, ',', '.') }}</span>
            <span class="text-[10px] text-gray-500">Custo/Mês</span>
        </div>
    </div>
</div>
@endif

{{-- VMs --}}
@if($data['allVms']->isNotEmpty())
<div class="mb-2 flex items-center gap-2">
    <x-heroicon-m-server-stack class="w-4 h-4 text-primary-400"/>
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">
        Máquinas Virtuais ({{ $data['allVms']->count() }})
    </h3>
</div>
<div style="display:grid;grid-template-columns:repeat(3,minmax(300px,1fr));gap:0.875rem;margin-bottom:1.25rem;">
    @foreach($data['allVms'] as $vm)
    <div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-primary-900/30 border border-primary-800 flex items-center justify-center shrink-0">
                <x-heroicon-m-computer-desktop class="w-4 h-4 text-primary-400"/>
            </div>
            <div class="min-w-0">
                <p class="font-bold text-white text-xs leading-tight truncate">{{ $vm->name }}</p>
                <div class="flex items-center gap-1 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $vm->status === 'active' ? 'bg-success-400' : 'bg-gray-500' }}"></span>
                    <span class="text-[10px] {{ $vm->status === 'active' ? 'text-success-400' : 'text-gray-500' }}">
                        {{ $vm->status === 'active' ? 'Ligado' : 'Desligado' }}
                    </span>
                </div>
            </div>
        </div>
        @if($vm->osDistribution)
        <div class="px-4 pt-2 pb-1">
            <p class="text-[10px] text-gray-300 font-medium">{{ $vm->osDistribution->name }}</p>
        </div>
        @endif
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-top:1px solid #1f2937;border-bottom:1px solid #1f2937;">
            <div class="py-2.5 flex flex-col items-center gap-0.5 border-r border-gray-800">
                <x-heroicon-m-cpu-chip class="w-3 h-3 text-blue-400 mb-0.5"/>
                <span class="text-sm font-bold text-white leading-none">{{ $vm->cpu_cores }}</span>
                <span class="text-[9px] text-gray-400">vCPUs</span>
            </div>
            <div class="py-2.5 flex flex-col items-center gap-0.5 border-r border-gray-800">
                <x-heroicon-m-circle-stack class="w-3 h-3 text-violet-400 mb-0.5"/>
                <span class="text-sm font-bold text-white leading-none">{{ $vm->ram_gb }}GB</span>
                <span class="text-[9px] text-gray-400">Memória</span>
            </div>
            <div class="py-2.5 flex flex-col items-center gap-0.5">
                <x-heroicon-m-server class="w-3 h-3 text-amber-400 mb-0.5"/>
                <span class="text-sm font-bold text-white leading-none">
                    {{ $vm->disk_os_gb + $vm->additionalDisks->sum('size_gb') }}GB
                </span>
                <span class="text-[9px] text-gray-400">Storage</span>
            </div>
        </div>
        <div class="mt-auto px-4 py-2.5 border-t border-gray-800 bg-gray-800/40 flex items-center justify-between">
            <span class="text-[10px] text-gray-500">Total/mês</span>
            <span class="text-sm font-bold text-primary-400">
                {{ $sym }} {{ number_format($vm->price_total_monthly / $rate, 2, ',', '.') }}
            </span>
        </div>
    </div>
    @endforeach
</div>
@elseif(!$data['rede'])
<div class="rounded-xl border-2 border-dashed border-gray-700 bg-gray-900/50 p-12 flex flex-col items-center gap-4 mb-5">
    <x-heroicon-o-server-stack class="text-gray-600" style="width:3rem;height:3rem;"/>
    <p class="text-gray-400 font-semibold">Nenhuma infraestrutura configurada</p>
</div>
@endif

{{-- S3 --}}
@if($data['s3_contracts']->isNotEmpty())
<div class="mb-2 flex items-center gap-2">
    <x-heroicon-m-archive-box class="w-4 h-4 text-yellow-400"/>
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">
        Armazenamento S3 ({{ $data['s3_contracts']->count() }})
    </h3>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1.25rem;">
    @foreach($data['s3_contracts'] as $s3)
    <div class="rounded-xl border border-yellow-900/40 bg-gray-900 overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-yellow-900/30 border border-yellow-800 flex items-center justify-center shrink-0">
                <x-heroicon-m-archive-box class="w-4 h-4 text-yellow-400"/>
            </div>
            <div>
                <p class="font-bold text-white text-xs">S3</p>
                <p class="text-[10px] text-gray-500">{{ $s3->size_gb }} GB</p>
            </div>
        </div>
        <div class="flex-1 px-4 py-3 flex items-center justify-center">
            <div class="text-center">
                <p class="text-3xl font-bold text-white">{{ $s3->size_gb }}<span class="text-sm font-normal text-gray-400"> GB</span></p>
                <p class="text-[10px] text-gray-500 mt-1">Object Storage</p>
            </div>
        </div>
        <div class="px-4 py-2.5 border-t border-gray-800 bg-gray-800/40 flex items-center justify-end">
            <span class="text-sm font-bold text-yellow-400">
                {{ $sym }} {{ number_format(($s3->size_gb * $s3->price_per_gb) / $rate, 2, ',', '.') }}/mês
            </span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Backup --}}
@if($data['backup_contracts']->isNotEmpty())
<div class="mb-2 flex items-center gap-2">
    <x-heroicon-m-shield-check class="w-4 h-4 text-green-400"/>
    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">
        Backup Gerenciado ({{ $data['backup_contracts']->count() }})
    </h3>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1.25rem;">
    @foreach($data['backup_contracts'] as $bkp)
    <div class="rounded-xl border border-green-900/40 bg-gray-900 overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-green-900/30 border border-green-800 flex items-center justify-center shrink-0">
                <x-heroicon-m-shield-check class="w-4 h-4 text-green-400"/>
            </div>
            <div>
                <p class="font-bold text-white text-xs">Backup</p>
                <p class="text-[10px] text-gray-500">{{ $bkp->machines ?? 1 }} máquina(s)</p>
            </div>
        </div>
        <div class="mt-auto px-4 py-2.5 border-t border-gray-800 bg-gray-800/40 flex items-center justify-end">
            <span class="text-sm font-bold text-green-400">
                {{ $sym }} {{ number_format($bkp->monthly_value / $rate, 2, ',', '.') }}/mês
            </span>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>
</x-filament-panels::page>
