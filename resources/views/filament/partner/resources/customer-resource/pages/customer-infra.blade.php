<x-filament-panels::page>
<style>.infra-dark input:not([type=checkbox]):not([type=radio]),.infra-dark textarea{background-color:#1f2937!important;color:white!important;border-color:#4b5563!important;color-scheme:dark;}</style>
<style>
.infra-dark input:not([type=checkbox]):not([type=radio]),.infra-dark textarea{background-color:#1f2937!important;color:white!important;border-color:#4b5563!important;color-scheme:dark;}
</style>
<div class="infra-dark">
@php
    $record  = $record ?? $this->customer;
    $data    = $this->getInfraData();
    $selects = $this->getSelects();
    $osDistribuicoes = $this->getOsDistribuicoes();
    $rdsModes        = $this->getRdsModes();
    $ic = 'w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500'; $icStyle = 'color-scheme:dark;background-color:#1f2937;color:white;border:1px solid #4b5563;';
    $sc = 'w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary-500';
    $lc = 'block text-[10px] font-bold text-gray-400 mb-1 uppercase tracking-widest';
    $bp = 'inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-semibold rounded-lg transition-colors';
    $bd = 'inline-flex items-center gap-1.5 px-4 py-2 bg-red-700 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors';
    $bg = 'inline-flex items-center gap-1.5 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-semibold rounded-lg transition-colors';
    $totalSteps = 7;
    $vmSteps    = [1=>'Sistema',2=>'SO',3=>'Recursos',4=>'Discos',5=>'Terminal',6=>'Segurança',7=>'Backup'];
@endphp

{{-- Sub-navbar --}}
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
        <a href="{{ \App\Filament\Partner\Resources\CustomerResource\Pages\CustomerDashboard::getUrl(['record'=>$record]) }}"
           class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-white transition-colors">
            <x-heroicon-m-chevron-left class="w-4 h-4"/>Dashboard
        </a>
        <span class="text-gray-700">/</span>
        <span class="text-sm font-bold text-white flex items-center gap-2">
            <x-heroicon-o-server-stack class="w-4 h-4 text-primary-400"/>Centro de Dados Virtual
        </span>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" wire:click="abrirProposta"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-emerald-500/40 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition-colors">
            <x-heroicon-m-document-text class="w-4 h-4"/>Gerar Proposta
        </button>
        <button type="button" wire:click="abrirEscolha" class="{{ $bp }}">
            <x-heroicon-m-plus class="w-4 h-4"/>Adicionar
        </button>
    </div>
</div>

{{-- Stats --}}
<div style="display:flex; gap:0.75rem;" class="mb-5">
    @foreach([['VMs Ativas',$data['vms_ativas'],'heroicon-o-server-stack','text-primary-400'],['vCPUs',$data['vcpu_total'],'heroicon-o-cpu-chip','text-gray-300'],['RAM Total',$data['ram_total'].' GB','heroicon-o-circle-stack','text-gray-300'],['Receita/Mês','R$ '.number_format($data['mrr_total'],2,',','.'),'heroicon-o-currency-dollar','text-success-400']] as $s)
    <div class="rounded-xl border border-gray-700 bg-gray-900 px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-800 border border-gray-700 flex items-center justify-center shrink-0"><x-dynamic-component :component="$s[2]" class="w-4 h-4 {{ $s[3] }}"/></div>
        <div><p class="text-[10px] text-gray-500 uppercase tracking-wide">{{ $s[0] }}</p><p class="text-sm font-bold {{ $s[3] }}">{{ $s[1] }}</p></div>
    </div>
    @endforeach
</div>

{{-- Rede --}}
@if($data['rede'])
@php $rede=$data['rede']; $isLan=$rede->networkType?->slug==='lan-to-lan'; $netCost=$isLan?($rede->networkType?->price??0):(($rede->extra_ip_price??0)+($rede->bandwidthOption?->price??0)); @endphp
<div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden mb-5">
    <div class="px-5 py-3 border-b border-gray-700 bg-gray-800/60 flex items-center justify-between">
        <div class="flex items-center gap-2"><x-heroicon-m-signal class="w-4 h-4 text-primary-400"/><span class="font-bold text-white text-sm">Rede do Cliente</span><span class="text-[10px] text-gray-500 bg-gray-800 border border-gray-700 px-2 py-0.5 rounded-full">Compartilhada entre todas as VMs</span></div>
        <button type="button" wire:click="abrirEditarRede" class="flex items-center gap-1 text-xs text-gray-500 hover:text-primary-400 transition-colors px-2 py-1 rounded hover:bg-primary-900/20"><x-heroicon-m-pencil-square class="w-3.5 h-3.5"/>Editar</button>
    </div>
    <div class="flex items-stretch divide-x divide-gray-800">
        <div class="flex-1 py-4 flex flex-col items-center gap-1"><x-heroicon-m-globe-alt class="w-5 h-5 text-primary-400 mb-0.5"/><span class="text-sm font-bold text-white">{{ $rede->networkType?->name??'—' }}</span><span class="text-[10px] text-gray-500">Tipo de Rede</span></div>
        <div class="flex-1 py-4 flex flex-col items-center gap-1"><x-heroicon-m-wifi class="w-5 h-5 text-primary-400 mb-0.5"/><span class="text-sm font-bold text-white">{{ $isLan?'1 Gbps':($rede->bandwidthOption?->name??'—') }}</span><span class="text-[10px] text-gray-500">Banda</span></div>
        <div class="flex-1 py-4 flex flex-col items-center gap-1"><x-heroicon-m-map-pin class="w-5 h-5 text-primary-400 mb-0.5"/><span class="text-sm font-bold text-white">{{ $isLan?'—':(1+($rede->extra_public_ips??0)) }}</span><span class="text-[10px] text-gray-500">{{ $isLan?'Topologia':'IP(s) Público(s)' }}</span></div>
        <div class="flex-1 py-4 flex flex-col items-center gap-1"><x-heroicon-m-currency-dollar class="w-5 h-5 text-success-400 mb-0.5"/><span class="text-sm font-bold text-success-400">R$ {{ number_format($netCost,2,',','.') }}</span><span class="text-[10px] text-gray-500">Custo/Mês</span></div>
    </div>
</div>
@endif

{{-- VMs --}}
@if($data['allVms']->isNotEmpty())
<div class="mb-2 flex items-center gap-2"><x-heroicon-m-server-stack class="w-4 h-4 text-primary-400"/><h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Máquinas Virtuais ({{ $data['allVms']->count() }})</h3></div>
<div style="display:grid;grid-template-columns:repeat(3,minmax(300px,1fr));gap:0.875rem;margin-bottom:1.25rem;">
    @foreach($data['allVms'] as $vm)
    <div class="rounded-xl border border-gray-700 bg-gray-900 hover:border-gray-600 transition-colors overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-primary-900/30 border border-primary-800 flex items-center justify-center shrink-0"><x-heroicon-m-computer-desktop class="w-4 h-4 text-primary-400"/></div>
                <div class="min-w-0"><p class="font-bold text-white text-xs leading-tight truncate">{{ $vm->name }}</p><div class="flex items-center gap-1 mt-0.5"><span class="w-1.5 h-1.5 rounded-full {{ $vm->status==='active'?'bg-success-400':'bg-gray-500' }}"></span><span class="text-[10px] {{ $vm->status==='active'?'text-success-400':'text-gray-500' }}">{{ $vm->status==='active'?'Ligado':'Desligado' }}</span></div></div>
            </div>
            <div class="flex items-center gap-0.5 shrink-0">
                <button type="button" wire:click="abrirEditarVm({{ $vm->id }})" class="p-1.5 rounded-md text-gray-600 hover:text-primary-400 hover:bg-primary-900/20 transition-colors"><x-heroicon-m-pencil-square class="w-3.5 h-3.5"/></button>
                <button type="button" wire:click="abrirExcluirVm({{ $vm->id }})" class="p-1.5 rounded-md text-gray-600 hover:text-red-400 hover:bg-red-900/20 transition-colors"><x-heroicon-m-trash class="w-3.5 h-3.5"/></button>
            </div>
        </div>
        @if($vm->osDistribution)<div class="px-4 pt-2 pb-1"><p class="text-[10px] text-gray-300 font-medium">{{ $vm->osDistribution->name }}</p></div>@endif
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-top:1px solid #1f2937;border-bottom:1px solid #1f2937;">
            <div class="py-2.5 flex flex-col items-center gap-0.5 border-r border-gray-800"><x-heroicon-m-cpu-chip class="w-3 h-3 text-blue-400 mb-0.5"/><span class="text-sm font-bold text-white leading-none">{{ $vm->cpu_cores }}</span><span class="text-[9px] text-gray-400">vCPUs</span><span class="text-[9px] text-blue-300 font-medium leading-none mt-0.5">R$ {{ number_format($vm->price_cpu,2,',','.') }}</span></div>
            <div class="py-2.5 flex flex-col items-center gap-0.5 border-r border-gray-800"><x-heroicon-m-circle-stack class="w-3 h-3 text-violet-400 mb-0.5"/><span class="text-sm font-bold text-white leading-none">{{ $vm->ram_gb }}GB</span><span class="text-[9px] text-gray-400">Memória</span><span class="text-[9px] text-violet-300 font-medium leading-none mt-0.5">R$ {{ number_format($vm->price_ram,2,',','.') }}</span></div>
            <div class="py-2.5 flex flex-col items-center gap-0.5"><x-heroicon-m-server class="w-3 h-3 text-amber-400 mb-0.5"/><span class="text-sm font-bold text-white leading-none">{{ $vm->disk_os_gb+$vm->additionalDisks->sum('size_gb') }}GB</span><span class="text-[9px] text-gray-400">Armazenamento</span><span class="text-[9px] text-amber-300 font-medium leading-none mt-0.5">R$ {{ number_format($vm->price_disk_os,2,',','.') }}</span></div>
        </div>
        <div class="px-3 py-2 space-y-1.5 flex-1">
            @if($vm->diskOsType)<div class="flex items-center justify-between text-[10px]"><span class="text-gray-500">Tipo Disco SO</span><span class="text-gray-300">{{ $vm->diskOsType->name }}</span></div>@endif
            @foreach($vm->additionalDisks as $d)<div class="flex items-center justify-between text-[10px]"><span class="text-gray-500">+ Disco Extra</span><span class="text-amber-300">{{ $d->size_gb }}GB · {{ $d->diskType?->name }}</span></div>@endforeach
            @if($vm->price_os_license>0)<div class="flex items-center justify-between text-[10px]"><span class="text-gray-500">Lic. Windows</span><span class="text-emerald-400">R$ {{ number_format($vm->price_os_license,2,',','.') }}</span></div>@endif
            @if($vm->price_rds>0)<div class="flex items-center justify-between text-[10px]"><span class="text-gray-500">Terminal ({{ $vm->rds_license_qty }}x)</span><span class="text-emerald-400">R$ {{ number_format($vm->price_rds,2,',','.') }}</span></div>@endif
            @if($vm->endpointSecurity)<div class="flex items-center justify-between text-[10px]"><span class="text-gray-500">Endpoint Security</span><span class="text-emerald-400">R$ {{ number_format($vm->price_endpoint,2,',','.') }}</span></div>@endif
            @if($vm->has_backup)<div class="flex items-center justify-between text-[10px]"><span class="text-gray-500">Backup</span><span class="text-emerald-400">R$ {{ number_format($vm->price_backup+$vm->price_backup_software,2,',','.') }}</span></div>@endif
        </div>
        <div class="mt-auto px-4 py-2.5 border-t border-gray-800 bg-gray-800/40 flex items-center justify-between"><span class="text-[10px] text-gray-500">Total/mês</span><span class="text-sm font-bold text-primary-400">R$ {{ number_format($vm->price_total_monthly,2,',','.') }}</span></div>
    </div>
    @endforeach
    <button type="button" wire:click="abrirEscolha" class="rounded-xl border-2 border-dashed border-gray-700 hover:border-primary-500/60 bg-transparent hover:bg-primary-950/10 flex flex-col items-center justify-center gap-3 py-8 text-gray-600 hover:text-primary-400 transition-all group min-h-[180px]">
        <div class="w-10 h-10 rounded-xl border-2 border-dashed border-current flex items-center justify-center group-hover:bg-primary-900/20 transition-colors"><x-heroicon-m-plus class="w-5 h-5"/></div>
        <span class="text-xs font-semibold">Adicionar</span>
    </button>
</div>
@elseif(!$data['rede'])
<div class="rounded-xl border-2 border-dashed border-gray-700 bg-gray-900/50 p-12 flex flex-col items-center gap-4 mb-5">
    <x-heroicon-o-server-stack class="text-gray-600" style="width:3rem;height:3rem;"/>
    <div class="text-center"><p class="text-gray-400 font-semibold">Nenhuma infraestrutura configurada</p><p class="text-gray-600 text-sm mt-1">Clique em "Adicionar" para começar</p></div>
    <button type="button" wire:click="abrirEscolha" class="{{ $bp }}"><x-heroicon-m-plus class="w-4 h-4"/>Adicionar</button>
</div>
@else
<div class="rounded-xl border-2 border-dashed border-gray-700 bg-gray-900/50 p-10 flex flex-col items-center gap-3 mb-5">
    <x-heroicon-o-server-stack class="text-gray-600" style="width:2.5rem;height:2.5rem;"/>
    <p class="text-gray-500 text-sm">Rede configurada. Adicione a primeira VM.</p>
    <button type="button" wire:click="abrirEscolha" class="{{ $bp }}"><x-heroicon-m-plus class="w-4 h-4"/>Adicionar VM</button>
</div>
@endif

{{-- S3 --}}
@if($data['s3_contracts']->isNotEmpty())
<div class="mb-2 flex items-center gap-2"><x-heroicon-m-archive-box class="w-4 h-4 text-yellow-400"/><h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Armazenamento S3 ({{ $data['s3_contracts']->count() }})</h3></div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1.25rem;">
    @foreach($data['s3_contracts'] as $s3)
    <div class="rounded-xl border border-yellow-900/40 bg-gray-900 hover:border-yellow-700/60 transition-colors overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-yellow-900/30 border border-yellow-800 flex items-center justify-center shrink-0"><x-heroicon-m-archive-box class="w-4 h-4 text-yellow-400"/></div><div><p class="font-bold text-white text-xs">{{ $s3->name }}</p><p class="text-[10px] text-gray-500">{{ $s3->size_gb }} GB</p></div></div>
            <div class="flex items-center gap-0.5 shrink-0">
                <button type="button" wire:click="abrirEditarS3({{ $s3->id }})" class="p-1.5 rounded-md text-gray-600 hover:text-yellow-400 hover:bg-yellow-900/20 transition-colors"><x-heroicon-m-pencil-square class="w-3.5 h-3.5"/></button>
                <button type="button" wire:click="abrirExcluirS3({{ $s3->id }})" class="p-1.5 rounded-md text-gray-600 hover:text-red-400 hover:bg-red-900/20 transition-colors"><x-heroicon-m-trash class="w-3.5 h-3.5"/></button>
            </div>
        </div>
        <div class="flex-1 px-4 py-3 flex items-center justify-center"><div class="text-center"><p class="text-3xl font-bold text-white">{{ $s3->size_gb }}<span class="text-sm font-normal text-gray-400"> GB</span></p><p class="text-[10px] text-gray-500 mt-1">Object Storage</p></div></div>
        <div class="px-4 py-2.5 border-t border-gray-800 bg-gray-800/40 flex items-center justify-end"><span class="text-sm font-bold text-yellow-400">R$ {{ number_format($s3->size_gb*$s3->price_per_gb,2,',','.') }}/mês</span></div>
    </div>
    @endforeach
</div>
@endif

{{-- Backup --}}
@if($data['backup_contracts']->isNotEmpty())
<div class="mb-2 flex items-center gap-2"><x-heroicon-m-shield-check class="w-4 h-4 text-green-400"/><h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Backup Gerenciado ({{ $data['backup_contracts']->count() }})</h3></div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1.25rem;">
    @foreach($data['backup_contracts'] as $bkp)
    <div class="rounded-xl border border-green-900/40 bg-gray-900 hover:border-green-700/60 transition-colors overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-green-900/30 border border-green-800 flex items-center justify-center shrink-0"><x-heroicon-m-shield-check class="w-4 h-4 text-green-400"/></div><div><p class="font-bold text-white text-xs">{{ $bkp->name }}</p><span class="text-[10px] bg-green-900/30 border border-green-800/50 text-green-400 px-1.5 py-0.5 rounded-full leading-none mt-0.5 inline-block">{{ $bkp->network_label }}</span></div></div>
            <div class="flex items-center gap-0.5 shrink-0">
                <button type="button" wire:click="abrirEditarBackup({{ $bkp->id }})" class="p-1.5 rounded-md text-gray-600 hover:text-green-400 hover:bg-green-900/20 transition-colors"><x-heroicon-m-pencil-square class="w-3.5 h-3.5"/></button>
                <button type="button" wire:click="abrirExcluirBackup({{ $bkp->id }})" class="p-1.5 rounded-md text-gray-600 hover:text-red-400 hover:bg-red-900/20 transition-colors"><x-heroicon-m-trash class="w-3.5 h-3.5"/></button>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid #1f2937;">
            <div class="py-3 flex flex-col items-center gap-0.5 border-r border-gray-800"><x-heroicon-m-computer-desktop class="w-3 h-3 text-gray-500 mb-0.5"/><span class="text-base font-bold text-white leading-none">{{ $bkp->machine_count }}</span><span class="text-[9px] text-gray-500">Máquinas</span></div>
            <div class="py-3 flex flex-col items-center gap-0.5 border-r border-gray-800"><x-heroicon-m-server class="w-3 h-3 text-gray-500 mb-0.5"/><span class="text-base font-bold text-white leading-none">{{ $bkp->total_disk_gb }}GB</span><span class="text-[9px] text-gray-500">Disco Total</span></div>
            <div class="py-3 flex flex-col items-center gap-0.5"><x-heroicon-m-archive-box class="w-3 h-3 text-gray-500 mb-0.5"/><span class="text-base font-bold text-white leading-none">{{ number_format($bkp->backup_storage_gb,0) }}GB</span><span class="text-[9px] text-gray-500">Backup</span></div>
        </div>
        <div class="px-4 py-2 flex flex-wrap gap-1">
            @if($bkp->retention)<span class="text-[10px] text-gray-500 bg-gray-800 px-1.5 py-0.5 rounded">{{ $bkp->retention->name }}</span>@endif
            @if($bkp->software)<span class="text-[10px] text-gray-500 bg-gray-800 px-1.5 py-0.5 rounded">{{ $bkp->software->name }}</span>@endif
            @if($bkp->bandwidthOption)<span class="text-[10px] text-gray-500 bg-gray-800 px-1.5 py-0.5 rounded">{{ $bkp->bandwidthOption->name }}</span>@endif
        </div>
        <div class="px-4 py-2" style="border-top:1px solid rgba(31,41,55,0.6);background:rgba(31,41,55,0.2);"><p class="text-[10px] leading-relaxed" style="color:#4b5563;"><span style="color:#6b7280;font-weight:600;">Ciclo:</span> 1 Full + 6 Incrementais — no 7º dia inicia novo Full. Armazenamento calculado em 50% do disco total.</p></div><div class="mt-auto px-4 py-2.5 border-t border-gray-800 bg-gray-800/40 flex items-center justify-end"><span class="text-sm font-bold text-green-400">R$ {{ number_format($bkp->monthly_value,2,',','.') }}/mês</span></div>
    </div>
    @endforeach
</div>
@endif

{{-- ═══ MODAIS ═══════════════════════════════════════════════════════ --}}

{{-- Escolha --}}
@if($modalEscolha)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="fecharModais"></div>
    <div class="relative border border-gray-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl" style="background-color:#111827;">
        <div class="flex items-center justify-between mb-6"><h2 class="text-base font-bold text-white">O que deseja contratar?</h2><button type="button" wire:click="fecharModais" class="text-gray-500 hover:text-white"><x-heroicon-m-x-mark class="w-5 h-5"/></button></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
            <button type="button" wire:click="escolherModulo('vm')" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-700 bg-gray-800 hover:border-primary-500 hover:bg-primary-900/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-primary-900/40 border border-primary-800 group-hover:border-primary-500 flex items-center justify-center transition-colors"><x-heroicon-o-server-stack class="w-6 h-6 text-primary-400"/></div>
                <div class="text-center"><p class="font-bold text-white text-sm">Máquina Virtual</p><p class="text-[11px] text-gray-500 mt-0.5">VMs com rede dedicada</p></div>
            </button>
            <button type="button" wire:click="escolherModulo('s3')" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-700 bg-gray-800 hover:border-yellow-500 hover:bg-yellow-900/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-yellow-900/40 border border-yellow-800 group-hover:border-yellow-500 flex items-center justify-center transition-colors"><x-heroicon-o-archive-box class="w-6 h-6 text-yellow-400"/></div>
                <div class="text-center"><p class="font-bold text-white text-sm">Armazenamento S3</p><p class="text-[11px] text-gray-500 mt-0.5">Object storage escalável</p></div>
            </button>
            <button type="button" wire:click="escolherModulo('backup')" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-700 bg-gray-800 hover:border-green-500 hover:bg-green-900/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-green-900/40 border border-green-800 group-hover:border-green-500 flex items-center justify-center transition-colors"><x-heroicon-o-shield-check class="w-6 h-6 text-green-400"/></div>
                <div class="text-center"><p class="font-bold text-white text-sm">Backup Gerenciado</p><p class="text-[11px] text-gray-500 mt-0.5">Proteção de dados</p></div>
            </button>
            <div class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-800 bg-gray-900/50 opacity-50 cursor-not-allowed relative">
                <div class="w-12 h-12 rounded-xl bg-gray-800 border border-gray-700 flex items-center justify-center"><x-heroicon-o-globe-alt class="w-6 h-6 text-gray-500"/></div>
                <div class="text-center"><p class="font-bold text-gray-500 text-sm">Hospedagem</p><p class="text-[11px] text-gray-600 mt-0.5">Web, E-mail</p></div>
                <span class="absolute top-2 right-2 text-[9px] bg-gray-700 text-gray-400 px-1.5 py-0.5 rounded-full font-bold uppercase">Em breve</span>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Rede --}}
@if($modalRede || $modalEditarRede)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="fecharModais"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between"><h2 class="text-base font-bold text-white flex items-center gap-2"><x-heroicon-m-signal class="w-4 h-4 text-primary-400"/>{{ $modalRede ? 'Configurar Rede do Cliente' : 'Editar Rede' }}</h2><button type="button" wire:click="fecharModais" class="text-gray-500 hover:text-white"><x-heroicon-m-x-mark class="w-5 h-5"/></button></div>
        <div class="p-6 space-y-4">
            @if($modalRede)<div class="flex items-start gap-3 p-3 bg-primary-900/20 border border-primary-800 rounded-lg text-sm text-primary-300"><x-heroicon-m-information-circle class="w-4 h-4 shrink-0 mt-0.5"/><span>A rede é compartilhada por todas as VMs. Configure uma vez e adicione quantas VMs quiser.</span></div>@endif
            <div><label class="{{ $lc }}">Tipo de Rede</label><select wire:model.live="form_network_type_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['network_types'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>
            @if($this->showBandwidth())<div><label class="{{ $lc }}">Largura de Banda</label><select wire:model.live="form_bandwidth_option_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['bandwidth_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>@endif
            <div><label class="{{ $lc }}">IPs Públicos Adicionais</label><input type="number" wire:model="form_extra_public_ips" min="0" max="20" style="{{ $icStyle }}" class="{{ $ic }}" placeholder="0"/><p class="text-[10px] text-gray-500 mt-1">Além do IP padrão incluído gratuitamente.</p></div>
        </div>
        <div class="px-6 pb-5 flex gap-3 justify-end border-t border-gray-800 pt-4">
            <button type="button" wire:click="fecharModais" class="{{ $bg }}">Cancelar</button>
            @if($modalRede)<button type="button" wire:click="salvarRede" class="{{ $bp }}">Salvar e Configurar VM →</button>
            @else<button type="button" wire:click="salvarApenasRede" class="{{ $bp }}"><x-heroicon-m-check class="w-4 h-4"/>Salvar</button>@endif
        </div>
    </div>
</div>
@endif

{{-- VM Wizard --}}
@if($modalVm || $modalEditarVm)
@php $isEditar=$modalEditarVm; @endphp
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="fecharModais"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-3xl shadow-2xl flex flex-col" style="max-height:90vh">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between shrink-0"><h2 class="text-base font-bold text-white flex items-center gap-2"><x-heroicon-m-server-stack class="w-4 h-4 text-primary-400"/>{{ $isEditar?'Editar VM':'Nova Máquina Virtual' }}</h2><button type="button" wire:click="fecharModais" class="text-gray-500 hover:text-white"><x-heroicon-m-x-mark class="w-5 h-5"/></button></div>
        <div class="px-6 py-3 border-b border-gray-800 flex items-center gap-1 overflow-x-auto shrink-0">
            @foreach($vmSteps as $n=>$label)
            <button type="button" wire:click="wizardIr({{ $n }})" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors whitespace-nowrap {{ $wizardStep===$n?'bg-primary-600 text-white':($wizardStep>$n?'bg-gray-700/70 text-gray-300':'text-gray-600 hover:text-gray-400') }}">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0 {{ $wizardStep===$n?'bg-white/20':($wizardStep>$n?'bg-green-500 text-white':'bg-gray-700 text-gray-500') }}">@if($wizardStep>$n)✓@else{{ $n }}@endif</span>{{ $label }}
            </button>
            @if($n<$totalSteps)<span class="text-gray-700 shrink-0">›</span>@endif
            @endforeach
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5">
            @if($wizardStep===1)<p class="text-sm text-gray-400 mb-4">Selecione a família do sistema operacional:</p><div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">@foreach($selects['os_families'] as $id=>$name)<label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-all {{ $form_os_family_id==$id?'bg-primary-900/30 border-primary-500':'bg-gray-800 border-gray-700 hover:border-gray-500' }}"><input type="radio" wire:model.live="form_os_family_id" value="{{ $id }}" class="sr-only"/><div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 {{ $form_os_family_id==$id?'border-primary-400':'border-gray-600' }}">@if($form_os_family_id==$id)<span class="w-2.5 h-2.5 rounded-full bg-primary-400"></span>@endif</div><span class="font-semibold text-sm {{ $form_os_family_id==$id?'text-white':'text-gray-400' }}">{{ $name }}</span></label>@endforeach</div>@endif
            @if($wizardStep===2)<div><label class="{{ $lc }}">Distribuição / Versão *</label><select wire:model.live="form_os_distribution_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($osDistribuicoes as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select><p class="text-[10px] text-gray-500 mt-1">Windows possui custo de licença adicional.</p></div>@endif
            @if($wizardStep===3)<div class="space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="{{ $lc }}">Nome da VM *</label><input type="text" wire:model="form_vm_name" placeholder="ex: BTN-SRV-APP-01" class="{{ $ic }}"/></div><div><label class="{{ $lc }}">Descrição</label><input type="text" wire:model="form_vm_description" placeholder="ex: Servidor de Aplicação" class="{{ $ic }}"/></div></div><div class="grid grid-cols-4 gap-3"><div><label class="{{ $lc }}">vCPUs *</label><input type="number" wire:model="form_cpu_cores" min="1" class="{{ $ic }}"/></div><div><label class="{{ $lc }}">RAM (GB) *</label><input type="number" wire:model="form_ram_gb" min="1" class="{{ $ic }}"/></div><div><label class="{{ $lc }}">Disco SO (GB) *</label><input type="number" wire:model="form_disk_os_gb" min="20" class="{{ $ic }}"/></div><div><label class="{{ $lc }}">Tipo Disco *</label><select wire:model="form_disk_os_type_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Tipo...</option>@foreach($selects['disk_types'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div></div></div>@endif
            @if($wizardStep===4)<div class="space-y-4"><label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors"><input type="checkbox" wire:model.live="form_has_additional_disks" class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500"/><div><p class="text-sm font-semibold text-gray-300">Adicionar discos extras</p><p class="text-[11px] text-gray-500">Além do disco do SO</p></div></label>@if($form_has_additional_disks)<div class="space-y-2">@foreach($form_additional_disks as $idx=>$disk)<div class="flex items-end gap-3 p-3 bg-gray-800 rounded-lg border border-gray-700"><div class="flex-1"><label class="{{ $lc }}">Tipo</label><select wire:model="form_additional_disks.{{ $idx }}.disk_type_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Tipo...</option>@foreach($selects['disk_types'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div><div class="w-28"><label class="{{ $lc }}">GB</label><input type="number" wire:model="form_additional_disks.{{ $idx }}.size_gb" min="10" class="{{ $ic }}"/></div><button type="button" wire:click="removerDisco({{ $idx }})" class="p-2 text-red-400 hover:bg-red-900/20 rounded-lg mb-0.5 transition-colors"><x-heroicon-m-trash class="w-4 h-4"/></button></div>@endforeach<button type="button" wire:click="adicionarDisco" class="w-full py-2.5 border-2 border-dashed border-gray-700 hover:border-primary-500 rounded-lg text-xs font-semibold text-gray-500 hover:text-primary-400 transition-colors">+ Adicionar Disco</button></div>@endif</div>@endif
            @if($wizardStep===5)<div class="space-y-4"><label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors"><input type="checkbox" wire:model.live="form_has_remote_desktop" class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500"/><div><p class="text-sm font-semibold text-gray-300">Licenças de Terminal Remoto</p><p class="text-[11px] text-gray-500">RDS ou TsPlus para acesso remoto</p></div></label>@if($form_has_remote_desktop)<div class="space-y-3 pl-4 border-l-2 border-primary-800"><div><label class="{{ $lc }}">Tipo (RDS ou TsPlus)</label><select wire:model.live="form_remote_desktop_type_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['remote_types'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>@if(!empty($rdsModes))<div><label class="{{ $lc }}">Modo de Licença</label><select wire:model.live="form_rds_license_mode_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($rdsModes as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>@endif<div><label class="{{ $lc }}">Quantidade</label><input type="number" wire:model="form_rds_license_qty" min="1" style="{{ $icStyle }}" class="{{ $ic }}" style="max-width:120px"/></div></div>@endif</div>@endif
            @if($wizardStep===6)<div class="space-y-4"><label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors"><input type="checkbox" wire:model.live="form_has_endpoint" class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500"/><div><p class="text-sm font-semibold text-gray-300">Endpoint Security</p><p class="text-[11px] text-gray-500">Proteção antivírus e antimalware</p></div></label>@if($form_has_endpoint)<div class="pl-4 border-l-2 border-green-800"><label class="{{ $lc }}">Solução de Endpoint</label><select wire:model="form_endpoint_security_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['endpoint_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>@endif</div>@endif
            @if($wizardStep===7)<div class="space-y-4"><label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors"><input type="checkbox" wire:model.live="form_has_backup_vm" class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500"/><div><p class="text-sm font-semibold text-gray-300">Backup desta VM</p><p class="text-[11px] text-gray-500">Retenção e software configuráveis</p></div></label>@if($form_has_backup_vm)<div class="grid grid-cols-2 gap-4 pl-4 border-l-2 border-yellow-800"><div><label class="{{ $lc }}">Retenção</label><select wire:model="form_backup_retention_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['retention_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div><div><label class="{{ $lc }}">Software de Backup</label><select wire:model="form_backup_software_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['backup_sw_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div></div>@endif</div>@endif
        </div>
        <div class="px-6 py-4 border-t border-gray-800 flex items-center justify-between shrink-0">
            <button type="button" wire:click="wizardPrev" class="{{ $wizardStep>1?$bg:'invisible' }}">← Anterior</button>
            <div class="flex items-center gap-3"><span class="text-xs text-gray-600">{{ $wizardStep }}/{{ $totalSteps }}</span>@if($wizardStep<$totalSteps)<button type="button" wire:click="wizardNext" class="{{ $bp }}">Próximo →</button>@else<button type="button" wire:click="{{ $isEditar?'salvarEditarVm':'salvarVm' }}" class="{{ $bp }}"><x-heroicon-m-check class="w-4 h-4"/>Salvar VM</button>@endif</div>
        </div>
    </div>
</div>
@endif

{{-- S3 --}}
@if($modalS3 || $modalEditarS3)
@php $isEditarS3=$modalEditarS3; @endphp
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="fecharModais"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between"><h2 class="text-base font-bold text-white flex items-center gap-2"><x-heroicon-o-archive-box class="w-4 h-4 text-yellow-400"/>{{ $isEditarS3?'Editar S3':'Armazenamento S3' }}</h2><button type="button" wire:click="fecharModais" class="text-gray-500 hover:text-white"><x-heroicon-m-x-mark class="w-5 h-5"/></button></div>
        <div class="p-6 space-y-4"><div><label class="{{ $lc }}">Armazenamento desejado (GB) *</label><input type="number" wire:model="form_s3_storage_gb" min="1" style="{{ $icStyle }}" class="{{ $ic }}" placeholder="100"/><p class="text-[10px] text-gray-500 mt-1">Você pode aumentar a qualquer momento.</p></div><div><label class="{{ $lc }}">Observações</label><textarea wire:model="form_s3_notes" rows="3" style="{{ $icStyle }}" class="{{ $ic }}" placeholder="Uso pretendido, projeto relacionado..."></textarea></div></div>
        <div class="px-6 pb-5 flex gap-3 justify-end border-t border-gray-800 pt-4"><button type="button" wire:click="fecharModais" class="{{ $bg }}">Cancelar</button><button type="button" wire:click="{{ $isEditarS3?'salvarEditarS3':'salvarS3' }}" class="{{ $bp }}"><x-heroicon-m-check class="w-4 h-4"/>{{ $isEditarS3?'Salvar':'Contratar S3' }}</button></div>
    </div>
</div>
@endif

{{-- Backup --}}
@if($modalBackup || $modalEditarBackup)
@php $isEditarBkp=$modalEditarBackup; @endphp
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="fecharModais"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between"><h2 class="text-base font-bold text-white flex items-center gap-2"><x-heroicon-o-shield-check class="w-4 h-4 text-green-400"/>{{ $isEditarBkp?'Editar Backup':'Backup Gerenciado' }}</h2><button type="button" wire:click="fecharModais" class="text-gray-500 hover:text-white"><x-heroicon-m-x-mark class="w-5 h-5"/></button></div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="{{ $lc }}">Tipo de Rede *</label><select wire:model="form_bkp_network" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['network_types'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach<option value="vpn_client">VPN Client</option></select></div>
                <div><label class="{{ $lc }}">Banda</label><select wire:model="form_bkp_bandwidth_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['bandwidth_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2"><label class="{{ $lc }}">Máquinas para Backup</label><button type="button" wire:click="adicionarMaquinaBackup" class="text-xs bg-gray-700 hover:bg-gray-600 text-primary-400 px-2 py-1 rounded-lg flex items-center gap-1"><x-heroicon-m-plus class="w-3 h-3"/>Adicionar Máquina</button></div>
                @forelse($form_bkp_machines_detail as $idx => $maq)
                <div class="flex gap-2 items-center mb-2">
                    <input type="text" wire:model.lazy="form_bkp_machines_detail.{{ $idx }}.descricao" placeholder="Nome da máquina" class="{{ $ic }}" style="{{ $icStyle ?? 'color-scheme:dark;background-color:#1f2937;color:white;border:1px solid #4b5563;' }}"/>
                    <input type="number" wire:model.lazy="form_bkp_machines_detail.{{ $idx }}.disk_gb" min="10" placeholder="GB" style="color-scheme:dark;background-color:#1f2937;color:white;border:1px solid #4b5563;" class="w-24 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary-500 shrink-0"/>
                    <button type="button" wire:click="removerMaquinaBackup({{ $idx }})" class="text-red-400 hover:text-red-300 shrink-0"><x-heroicon-m-x-mark class="w-4 h-4"/></button>
                </div>
                @empty
                <p class="text-xs text-gray-500 italic py-2">Nenhuma máquina adicionada. Clique em Adicionar.</p>
                @endforelse
                @if(count($form_bkp_machines_detail) > 0)
                <p class="text-[10px] text-gray-500 mt-1">{{ $form_bkp_machines }} máquina(s) — {{ $form_bkp_disk_gb }} GB total</p>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4"><div><label class="{{ $lc }}">Retenção</label><select wire:model="form_bkp_retention_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['retention_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div><div><label class="{{ $lc }}">Software de Backup</label><select wire:model="form_bkp_software_id" style="color-scheme:dark;background-color:#1f2937;" class="{{ $sc }}"><option value="">Selecione...</option>@foreach($selects['backup_sw_options'] as $id=>$name)<option value="{{ $id }}">{{ $name }}</option>@endforeach</select></div></div>
        </div>
        <div class="px-6 pb-5 flex gap-3 justify-end border-t border-gray-800 pt-4"><button type="button" wire:click="fecharModais" class="{{ $bg }}">Cancelar</button><button type="button" wire:click="{{ $isEditarBkp?'salvarEditarBackup':'salvarBackup' }}" class="{{ $bp }}"><x-heroicon-m-check class="w-4 h-4"/>{{ $isEditarBkp?'Salvar':'Contratar Backup' }}</button></div>
    </div>
</div>
@endif

{{-- Exclusões --}}
@foreach([['modalExcluirVm','VM','confirmarExcluirVm'],['modalExcluirS3','S3','confirmarExcluirS3'],['modalExcluirBackup','Backup','confirmarExcluirBackup']] as [$mp,$label,$method])
@if($$mp)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="fecharModais"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-red-900/40 border border-red-800 flex items-center justify-center"><x-heroicon-m-trash class="w-5 h-5 text-red-400"/></div><h2 class="text-base font-bold text-white">Excluir {{ $label }}</h2></div>
        <p class="text-sm text-gray-400 mb-6">Tem certeza? Esta ação <strong class="text-white">não pode ser desfeita</strong>.</p>
        <div class="flex gap-3 justify-end"><button type="button" wire:click="fecharModais" class="{{ $bg }}">Cancelar</button><button type="button" wire:click="{{ $method }}" class="{{ $bd }}"><x-heroicon-m-trash class="w-4 h-4"/>Sim, excluir</button></div>
    </div>
</div>
@endif
@endforeach

{{-- Modal: Gerar Proposta --}}
@if($modalProposta)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.65);backdrop-filter:blur(4px);" wire:click="$set('modalProposta',false)"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-y-auto" style="max-height:90vh;">
        <div class="sticky top-0 bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-900/40 border border-emerald-800 flex items-center justify-center">
                    <x-heroicon-m-document-text class="w-5 h-5 text-emerald-400"/>
                </div>
                <h2 class="text-base font-bold text-white">Gerar Proposta</h2>
            </div>
            <button wire:click="$set('modalProposta',false)" class="text-gray-400 hover:text-white transition-colors">
                <x-heroicon-m-x-mark class="w-5 h-5"/>
            </button>
        </div>

        <div class="px-6 py-4 space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Título da Proposta</label>
                    <input type="text" wire:model="proposta_titulo"
                        class="w-full rounded-lg bg-gray-800 border border-gray-700 text-sm text-white px-3 py-2 focus:ring-1 focus:ring-emerald-500 outline-none"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Válida até</label>
                    <input type="date" wire:model="proposta_validade"
                        class="w-full rounded-lg bg-gray-800 border border-gray-700 text-sm text-white px-3 py-2 focus:ring-1 focus:ring-emerald-500 outline-none"/>
                </div>
            </div>

            @if($data['rede'])
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-m-signal class="w-4 h-4 text-primary-400"/>
                    <span class="text-[11px] font-semibold text-primary-400 uppercase tracking-wide">Serviços de Rede</span>
                </div>
                <div class="rounded-lg border border-gray-700 bg-gray-800/40 p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-white">Rede</span>
                            <span class="text-[10px] text-gray-400">{{ $data['rede']->networkType?->name }}</span>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="proposta_incluir_rede" class="rounded border-gray-600 text-emerald-500"/>
                            <span class="text-[11px] text-gray-400">Incluir na proposta</span>
                        </label>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-4"></div>
            </div>
            @endif

            @if($data['allVms']->isNotEmpty())
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-m-server-stack class="w-4 h-4 text-primary-400"/>
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Máquinas Virtuais</span>
                </div>
                <div class="space-y-2">
                    @foreach($data['allVms'] as $vm)
                    <label class="flex items-center justify-between rounded-lg border border-gray-700 bg-gray-800/40 px-3 py-2.5 cursor-pointer hover:border-gray-600 hover:bg-gray-800/60 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" wire:model="proposta_vm_ids" value="{{ $vm->id }}" class="rounded border-gray-600 text-emerald-500"/>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $vm->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $vm->osDistribution?->name }} · {{ $vm->cpu_cores }} vCPUs · {{ $vm->ram_gb }}GB RAM</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-emerald-400 shrink-0 ml-3">R$ {{ number_format($vm->price_total_monthly,2,',','.') }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if($data['s3_contracts']->isNotEmpty())
            <div>
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Object Storage (S3)</div>
                <div class="space-y-2">
                    @foreach($data['s3_contracts'] as $s3)
                    <label class="flex items-center justify-between rounded-lg border border-gray-700 bg-gray-800/40 px-3 py-2.5 cursor-pointer hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" wire:model="proposta_s3_ids" value="{{ $s3->id }}" class="rounded border-gray-600 text-emerald-500"/>
                            <div>
                                <p class="text-sm font-semibold text-white">S3 · {{ $s3->size_gb }} GB</p>
                                <p class="text-[10px] text-gray-400">R$ {{ number_format($s3->price_per_gb,4,',','.') }}/GB</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-emerald-400 shrink-0 ml-3">R$ {{ number_format($s3->size_gb*$s3->price_per_gb,2,',','.') }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if($data['backup_contracts']->isNotEmpty())
            <div>
                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Contratos de Backup</div>
                <div class="space-y-2">
                    @foreach($data['backup_contracts'] as $bkp)
                    <label class="flex items-center justify-between rounded-lg border border-gray-700 bg-gray-800/40 px-3 py-2.5 cursor-pointer hover:border-gray-600 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" wire:model="proposta_backup_ids" value="{{ $bkp->id }}" class="rounded border-gray-600 text-emerald-500"/>
                            <div>
                                <p class="text-sm font-semibold text-white">Backup Gerenciado</p>
                                <p class="text-[10px] text-gray-400">{{ $bkp->machine_count ?? 1 }} máquina(s) · {{ $bkp->total_disk_gb ?? 0 }}GB</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-emerald-400 shrink-0 ml-3">R$ {{ number_format($bkp->monthly_value,2,',','.') }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            @php
                $__calcTotal = 0;
                $__calcItens = [];
                if($proposta_incluir_rede && $data['rede']) {
                    $__r = $data['rede'];
                    $__isLan = $__r->networkType?->slug === 'lan-to-lan';
                    $__netCost = $__isLan ? (float)($__r->networkType?->price ?? 0) : ((float)($__r->extra_ip_price ?? 0) + (float)($__r->bandwidthOption?->price ?? 0));
                    $__calcItens[] = ['item' => 'Rede', 'detalhes' => $__r->networkType?->name ?? '', 'valor' => $__netCost];
                    $__calcTotal += $__netCost;
                }
                foreach($data['allVms']->whereIn('id', $proposta_vm_ids) as $__v) {
                    $__calcItens[] = ['item' => $__v->name, 'detalhes' => $__v->cpu_cores.' vCPUs · '.$__v->ram_gb.'GB RAM', 'valor' => $__v->price_total_monthly];
                    $__calcTotal += $__v->price_total_monthly;
                }
                foreach($data['s3_contracts']->whereIn('id', $proposta_s3_ids) as $__s) {
                    $__sv = $__s->size_gb * $__s->price_per_gb;
                    $__calcItens[] = ['item' => 'S3 · '.$__s->size_gb.' GB', 'detalhes' => 'R$ '.number_format($__s->price_per_gb,4,',','.').'/GB', 'valor' => $__sv];
                    $__calcTotal += $__sv;
                }
                foreach($data['backup_contracts']->whereIn('id', $proposta_backup_ids) as $__b) {
                    $__calcItens[] = ['item' => 'Backup Gerenciado', 'detalhes' => ($__b->machine_count ?? 1).' máquina(s) · '.($__b->total_disk_gb ?? 0).'GB', 'valor' => $__b->monthly_value];
                    $__calcTotal += $__b->monthly_value;
                }
                $__pct = max(0, min(100, (float)$proposta_desconto));
                $__descontoValor = $__pct > 0 ? round($__calcTotal * ($__pct / 100), 2) : 0;
                $__totalFinal = $__calcTotal - $__descontoValor;
            @endphp

            {{-- Campo de Desconto --}}
            <div class="bg-orange-900/20 border border-orange-700/50 rounded-lg p-3">
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold text-orange-400 uppercase tracking-wide mb-1">Desconto (%)</label>
                        <input type="number" wire:model="proposta_desconto" min="0" max="100" step="0.5"
                            class="w-full rounded-lg bg-gray-800 border border-gray-700 text-sm text-white px-3 py-2 focus:ring-1 focus:ring-orange-500 outline-none"/>
                    </div>
                    @if($__pct > 0)
                    <div class="text-right shrink-0">
                        <span class="text-[10px] text-orange-400 font-semibold">Economia</span>
                        <p class="text-sm font-bold text-orange-300">R$ {{ number_format($__descontoValor,2,',','.') }}/mês</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Grid de Resumo --}}
            @if(count($__calcItens) > 0)
            <div class="rounded-lg border border-gray-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-800 border-b border-gray-700">
                            <th class="text-left px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wide">Item</th>
                            <th class="text-left px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wide">Detalhes</th>
                            <th class="text-right px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wide">Valor/mês</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($__calcItens as $__item)
                        <tr class="border-b border-gray-700/50 hover:bg-gray-800/30">
                            <td class="px-3 py-2 text-white font-semibold">{{ $__item['item'] }}</td>
                            <td class="px-3 py-2 text-gray-400 text-xs">{{ $__item['detalhes'] }}</td>
                            <td class="px-3 py-2 text-emerald-400 font-bold text-right">R$ {{ number_format($__item['valor'],2,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-emerald-900/20 border-t border-gray-700">
                            <td colspan="2" class="px-3 py-2 text-white font-bold">Total Geral</td>
                            <td class="px-3 py-2 text-emerald-400 font-bold text-right text-base">R$ {{ number_format($__calcTotal,2,',','.') }}</td>
                        </tr>
                        @if($__pct > 0)
                        <tr class="bg-red-900/20 border-t border-gray-700/50">
                            <td colspan="2" class="px-3 py-2 text-orange-400 font-semibold">Desconto ({{ $__pct }}%)</td>
                            <td class="px-3 py-2 text-orange-400 font-bold text-right">- R$ {{ number_format($__descontoValor,2,',','.') }}</td>
                        </tr>
                        <tr class="bg-emerald-900/30 border-t border-gray-700">
                            <td colspan="2" class="px-3 py-2 text-white font-bold">Total com Desconto</td>
                            <td class="px-3 py-2 text-emerald-300 font-bold text-right text-base">R$ {{ number_format($__totalFinal,2,',','.') }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Observações (opcional)</label>
                <textarea wire:model="proposta_notas" rows="3"
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 text-sm text-white px-3 py-2 focus:ring-1 focus:ring-emerald-500 outline-none resize-none placeholder-gray-500"
                    placeholder="Condições especiais, prazo, observações..."></textarea>
            </div>
        </div>

        <div class="sticky bottom-0 bg-gray-900 border-t border-gray-800 px-6 py-4 flex gap-3 justify-end">
            <button type="button" wire:click="$set('modalProposta',false)" class="{{ $bg }}">Cancelar</button>
            <button type="button" wire:click="gerarProposta"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors">
                <x-heroicon-m-document-text class="w-4 h-4"/>Gerar e Abrir Proposta
            </button>
        </div>
    </div>
</div>
@endif


{{-- Botão Ver Preços por projeto --}}
@foreach($data['projects'] as $proj)
<div class="mt-3 flex justify-end">
    <button type="button" wire:click="viewProjectPricing({{ $proj->id }})"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-primary-500/40 bg-primary-500/10 text-primary-400 hover:bg-primary-500/20 transition-colors">
        <x-heroicon-o-currency-dollar class="w-4 h-4"/> 💰 Ver Preços e Aplicar Descontos
    </button>
</div>
@endforeach

{{-- Incluir o modal de pricing --}}
@include('filament.partner.resources.customer-resource.pages.customer-infra-pricing-modal')

</x-filament-panels::page>
