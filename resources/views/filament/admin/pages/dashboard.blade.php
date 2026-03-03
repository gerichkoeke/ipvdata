<x-filament-panels::page>
@php $d = $this->getDashboardData(); @endphp

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="col-span-2 rounded-xl border border-gray-700 bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">MRR Total</p>
        <p class="text-2xl font-bold text-emerald-400">R$ {{ number_format($d['mrr_total'],2,',','.') }}</p>
        <div class="flex gap-3 mt-2 text-[10px] text-gray-500">
            <span>VMs: R$ {{ number_format($d['mrr_vms'],2,',','.') }}</span>
            <span>S3: R$ {{ number_format($d['mrr_s3'],2,',','.') }}</span>
            <span>Backup: R$ {{ number_format($d['mrr_backup'],2,',','.') }}</span>
        </div>
    </div>
    @foreach([
        ['Parceiros Ativos', $d['parceiros_ativos'], 'text-blue-400'],
        ['Clientes Ativos', $d['clientes_ativos'], 'text-indigo-400'],
        ['VMs Ativas', $d['vms_ativas'], 'text-primary-400'],
        ['Distribuidores', $d['distribuidores_ativos'], 'text-purple-400'],
        ['Propostas Aprovadas', $d['propostas_aprovadas'], 'text-emerald-400'],
    ] as [$label, $value, $color])
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ $label }}</p>
        <p class="text-2xl font-bold {{ $color }}">{{ $value }}</p>
    </div>
    @endforeach
</div>

{{-- Gráficos --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-white mb-4">Evolução MRR — últimos 6 meses</h3>
        <canvas id="adminMrrChart" height="200"></canvas>
    </div>
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-white mb-4">Distribuição de Receita</h3>
        <canvas id="adminRevenueChart" height="200"></canvas>
    </div>
</div>

{{-- Tabelas --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Top Parceiros --}}
    <div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-700">
            <h3 class="text-sm font-bold text-white">Top 5 Parceiros por MRR</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-800 border-b border-gray-700">
                    <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Parceiro</th>
                    <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Clientes</th>
                    <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">MRR</th>
                </tr>
            </thead>
            <tbody>
                @forelse($d['top_partners'] as $p)
                <tr class="border-b border-gray-700/50 hover:bg-gray-800/30">
                    <td class="px-4 py-2 text-white font-medium">{{ $p->trade_name ?? $p->company_name }}</td>
                    <td class="px-4 py-2 text-gray-400 text-right">{{ $p->customers_count }}</td>
                    <td class="px-4 py-2 text-emerald-400 font-bold text-right">R$ {{ number_format($p->mrr ?? 0,2,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-3 text-gray-500 text-center text-xs">Nenhum parceiro com projetos ativos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Últimas Propostas --}}
    <div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-700">
            <h3 class="text-sm font-bold text-white">Últimas 5 Propostas</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-800 border-b border-gray-700">
                    <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Número</th>
                    <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Cliente</th>
                    <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Total</th>
                    <th class="text-center px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($d['ultimas_propostas'] as $prop)
                @php
                $statusColor = match($prop->status) {
                    'approved' => 'text-emerald-400',
                    'sent' => 'text-blue-400',
                    'rejected' => 'text-red-400',
                    default => 'text-gray-400'
                };
                @endphp
                <tr class="border-b border-gray-700/50 hover:bg-gray-800/30">
                    <td class="px-4 py-2 text-gray-300 font-mono text-xs">{{ $prop->number }}</td>
                    <td class="px-4 py-2 text-white text-xs">{{ $prop->customer?->trade_name ?? $prop->customer?->name }}</td>
                    <td class="px-4 py-2 text-emerald-400 font-bold text-right text-xs">R$ {{ number_format($prop->total,2,',','.') }}</td>
                    <td class="px-4 py-2 text-center"><span class="text-xs {{ $statusColor }}">{{ ucfirst($prop->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-3 text-gray-500 text-center text-xs">Nenhuma proposta</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- VMs Recentes --}}
<div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-700">
        <h3 class="text-sm font-bold text-white">VMs Recentes (últimas 5)</h3>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-800 border-b border-gray-700">
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">VM</th>
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">OS</th>
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Cliente</th>
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Parceiro</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Valor/mês</th>
            </tr>
        </thead>
        <tbody>
            @forelse($d['ultimas_vms'] as $vm)
            <tr class="border-b border-gray-700/50 hover:bg-gray-800/30">
                <td class="px-4 py-2 text-white font-medium">{{ $vm->name }}</td>
                <td class="px-4 py-2 text-gray-400 text-xs">{{ $vm->osDistribution?->name ?? '-' }}</td>
                <td class="px-4 py-2 text-gray-300 text-xs">{{ $vm->project?->customer?->trade_name ?? $vm->project?->customer?->name ?? '-' }}</td>
                <td class="px-4 py-2 text-gray-300 text-xs">{{ $vm->project?->partner?->trade_name ?? $vm->project?->partner?->company_name ?? '-' }}</td>
                <td class="px-4 py-2 text-emerald-400 font-bold text-right text-xs">R$ {{ number_format($vm->price_total_monthly ?? 0,2,',','.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-3 text-gray-500 text-center text-xs">Nenhuma VM registrada</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const mrrData = @json($d['mrr_chart']);
    const gridColor = 'rgba(255,255,255,0.07)';
    const tickColor = 'rgba(255,255,255,0.4)';

    // MRR Line Chart
    const mrrCtx = document.getElementById('adminMrrChart');
    if (mrrCtx) {
        new Chart(mrrCtx, {
            type: 'line',
            data: {
                labels: mrrData.map(m => m.label),
                datasets: [{
                    label: 'MRR (R$)',
                    data: mrrData.map(m => m.value),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor } }
                }
            }
        });
    }

    // Revenue Donut Chart
    const revCtx = document.getElementById('adminRevenueChart');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'doughnut',
            data: {
                labels: ['VMs', 'S3', 'Backup'],
                datasets: [{
                    data: [{{ $d['mrr_vms'] }}, {{ $d['mrr_s3'] }}, {{ $d['mrr_backup'] }}],
                    backgroundColor: ['rgba(99,102,241,0.8)', 'rgba(16,185,129,0.8)', 'rgba(245,158,11,0.8)'],
                    borderColor: ['#6366f1', '#10b981', '#f59e0b'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: tickColor } } }
            }
        });
    }
})();
</script>
</x-filament-panels::page>
