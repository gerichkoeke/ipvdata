<x-filament-panels::page>
@php
    $d      = $this->getDashboardData();
    $record = $this->record;
    $rede   = $d['rede'];
@endphp

{{-- Barra de stats topo --}}
<div class="rounded-xl border border-gray-700 bg-gray-900 px-5 py-3 mb-5 overflow-x-auto">
    <div class="flex items-center gap-0 divide-x divide-gray-700" style="min-width:max-content">

        <div class="pr-6 flex items-center gap-3">
            <x-heroicon-o-cloud class="w-4 h-4 text-gray-400 shrink-0"/>
            <div>
                <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">{{ __('app.customer_infra.environment') }}</p>
                <p class="text-xs text-white font-bold leading-none">{{ $d['tem_infra'] ? '1 VDC' : '0 VDC' }}</p>
            </div>
        </div>

        <div class="px-6 flex items-center gap-5">
            <div class="flex items-center gap-2">
                <x-heroicon-o-server-stack class="w-4 h-4 text-primary-400 shrink-0"/>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">{{ __('app.customer_infra.active_vms') }}</p>
                    <p class="text-xs text-white font-bold leading-none">{{ $d['vms_ativas'] }}</p>
                </div>
            </div>
            @if($d['s3_total_gb'] > 0)
            <div class="flex items-center gap-2">
                <x-heroicon-o-archive-box class="w-4 h-4 text-yellow-400 shrink-0"/>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">S3</p>
                    <p class="text-xs text-white font-bold leading-none">{{ $d['s3_total_gb'] }} GB</p>
                </div>
            </div>
            @endif
            @if($d['backup_maquinas'] > 0)
            <div class="flex items-center gap-2">
                <x-heroicon-o-shield-check class="w-4 h-4 text-green-400 shrink-0"/>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">Backup</p>
                    <p class="text-xs text-white font-bold leading-none">{{ $d['backup_maquinas'] }} máq.</p>
                </div>
            </div>
            @endif
        </div>

        <div class="px-6 flex items-center gap-5">
            <div class="flex items-center gap-2">
                <x-heroicon-o-cpu-chip class="w-4 h-4 text-gray-400 shrink-0"/>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">CPU</p>
                    <p class="text-xs text-white font-bold leading-none">{{ $d['vcpu_total'] }} <span class="text-gray-500 font-normal">vCPU</span></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-heroicon-o-circle-stack class="w-4 h-4 text-gray-400 shrink-0"/>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">{{ __('app.customer_infra.total_ram') }}</p>
                    <p class="text-xs text-white font-bold leading-none">{{ $d['ram_total'] }} <span class="text-gray-500 font-normal">GB</span></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-heroicon-o-server class="w-4 h-4 text-gray-400 shrink-0"/>
                <div>
                    <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">{{ __('app.customer_infra.storage_total') }}</p>
                    <p class="text-xs text-white font-bold leading-none">{{ $d['disk_total'] }} <span class="text-gray-500 font-normal">GB</span></p>
                </div>
            </div>
        </div>

        <div class="pl-6 flex items-center gap-2">
            <x-heroicon-o-currency-dollar class="w-4 h-4 text-success-400 shrink-0"/>
            <div>
                <p class="text-[9px] uppercase tracking-widest text-gray-500 font-semibold leading-none mb-0.5">MRR</p>
                <p class="text-sm font-bold text-success-400 leading-none">R$ {{ number_format($d['mrr_total'], 2, ',', '.') }}</p>
            </div>
        </div>

    </div>
</div>

<p class="text-[10px] font-bold uppercase tracking-widest text-primary-400 mb-3">{{ strtoupper(__('app.customer_infra.virtual_datacenter')) }}</p>

{{-- Card VDC --}}
<a href="{{ $this->getInfraUrl() }}" class="block rounded-xl border border-gray-700 bg-gray-900 hover:border-primary-500/70 hover:bg-gray-800/50 transition-all group max-w-3xl">
    <div class="p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-900/40 border border-primary-700 group-hover:border-primary-400 flex items-center justify-center transition-colors shrink-0">
                    <x-heroicon-o-cloud class="w-4 h-4 text-primary-400"/>
                </div>
                <div>
                    <p class="font-bold text-white text-sm group-hover:text-primary-300 transition-colors">{{ $record->trade_name ?? $record->name }} VDC</p>
                    <div class="flex items-center gap-3 mt-0.5">
                        <span class="flex items-center gap-1 text-[10px] text-gray-500">
                            <x-heroicon-m-globe-alt class="w-3 h-3"/>{{ $record->partner?->trade_name ?? $record->partner?->company_name }}
                        </span>
                        @if($rede)
                        <span class="flex items-center gap-1 text-[10px] text-gray-500">
                            <x-heroicon-m-signal class="w-3 h-3"/>{{ $rede->networkType?->name ?? 'VPC' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <x-heroicon-m-chevron-right class="w-4 h-4 text-gray-600 group-hover:text-primary-400 transition-colors shrink-0"/>
        </div>

        <div class="flex items-stretch divide-x divide-gray-800 border border-gray-800 rounded-lg overflow-hidden">
            <div class="flex-1 px-4 py-3 flex items-center gap-3">
                <x-heroicon-o-server-stack class="w-4 h-4 text-primary-400 shrink-0"/>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase tracking-wide leading-none mb-0.5">Aplicativos</p>
                    <p class="text-base font-bold text-white leading-none">{{ $d['vms_ativas'] }}</p>
                    <p class="text-[9px] text-gray-600 mt-0.5">VMs em execução</p>
                </div>
            </div>
            <div class="flex-1 px-4 py-3 flex items-center gap-3">
                <x-heroicon-o-cpu-chip class="w-4 h-4 text-gray-400 shrink-0"/>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase tracking-wide leading-none mb-0.5">CPU</p>
                    <p class="text-base font-bold text-white leading-none">{{ $d['vcpu_total'] }}</p>
                    <p class="text-[9px] text-gray-600 mt-0.5">vCPUs</p>
                </div>
            </div>
            <div class="flex-1 px-4 py-3 flex items-center gap-3">
                <x-heroicon-o-circle-stack class="w-4 h-4 text-gray-400 shrink-0"/>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase tracking-wide leading-none mb-0.5">{{ __('app.customer_infra.total_ram') }}</p>
                    <p class="text-base font-bold text-white leading-none">{{ $d['ram_total'] }} <span class="text-xs font-normal text-gray-400">GB</span></p>
                    <p class="text-[9px] text-gray-600 mt-0.5">RAM alocada</p>
                </div>
            </div>
            <div class="flex-1 px-4 py-3 flex items-center gap-3">
                <x-heroicon-o-server class="w-4 h-4 text-gray-400 shrink-0"/>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase tracking-wide leading-none mb-0.5">Storage</p>
                    <p class="text-base font-bold text-white leading-none">{{ $d['disk_total'] }} <span class="text-xs font-normal text-gray-400">GB</span></p>
                    <p class="text-[9px] text-gray-600 mt-0.5">Disco alocado</p>
                </div>
            </div>
        </div>

        @if(!$d['tem_infra'])
        <div class="mt-4 flex items-center gap-2 text-gray-600 group-hover:text-primary-400 transition-colors">
            <x-heroicon-m-plus-circle class="w-4 h-4"/>
            <span class="text-xs font-semibold">Clique para configurar a infraestrutura</span>
        </div>
        @endif
    </div>

    @if($d['tem_infra'])
    <div class="px-5 py-3 border-t border-gray-800 bg-gray-800/40 rounded-b-xl flex items-center justify-between">
        <div class="flex items-center gap-5 text-[11px]">
            @if($d['mrr_vms'] > 0)
            <span class="flex items-center gap-1.5 text-gray-500">
                <x-heroicon-m-server-stack class="w-3 h-3 text-primary-400"/>VMs:
                <strong class="text-gray-300">R$ {{ number_format($d['mrr_vms'], 2, ',', '.') }}</strong>
            </span>
            @endif
            @if($d['mrr_s3'] > 0)
            <span class="flex items-center gap-1.5 text-gray-500">
                <x-heroicon-m-archive-box class="w-3 h-3 text-yellow-400"/>S3:
                <strong class="text-gray-300">R$ {{ number_format($d['mrr_s3'], 2, ',', '.') }}</strong>
            </span>
            @endif
            @if($d['mrr_backups'] > 0)
            <span class="flex items-center gap-1.5 text-gray-500">
                <x-heroicon-m-shield-check class="w-3 h-3 text-green-400"/>Backup:
                <strong class="text-gray-300">R$ {{ number_format($d['mrr_backups'], 2, ',', '.') }}</strong>
            </span>
            @endif
        </div>
        <span class="text-sm font-bold text-success-400">R$ {{ number_format($d['mrr_total'], 2, ',', '.') }}/mês</span>
    </div>
    @endif
</a>

@php $proposals = $record->proposals()->latest()->take(3)->get(); @endphp
@if($proposals->isNotEmpty())
<div class="mt-5 rounded-xl border border-gray-700 bg-gray-900 overflow-hidden max-w-3xl">
    <div class="px-5 py-3 border-b border-gray-700 bg-gray-800/60 flex items-center gap-2">
        <x-heroicon-o-document-text class="w-4 h-4 text-warning-400"/>
        <h3 class="font-semibold text-white text-sm">{{ __('app.customer_infra.latest_proposals') }}</h3>
    </div>
    @foreach($proposals as $proposal)
    <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-800/40 transition-colors border-b border-gray-800 last:border-0">
        <div>
            <p class="font-medium text-white text-sm">{{ $proposal->title }}</p>
            <p class="text-[11px] text-gray-500">#{{ $proposal->number }} · {{ $proposal->created_at->format('d/m/Y') }}</p>
        </div>
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border ml-4 shrink-0 {{ match($proposal->status) { 'approved' => 'bg-success-950 border-success-700 text-success-400', 'sent' => 'bg-blue-950 border-blue-700 text-blue-400', default => 'bg-gray-800 border-gray-600 text-gray-400' } }}">
            {{ match($proposal->status) { 'approved' => 'Aprovada', 'sent' => 'Enviada', 'draft' => 'Rascunho', default => $proposal->status } }}
        </span>
    </div>
    @endforeach
</div>
@endif

</x-filament-panels::page>
