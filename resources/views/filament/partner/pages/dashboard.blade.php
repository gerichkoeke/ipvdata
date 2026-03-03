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
        ['Clientes Ativos', $d['clientes_ativos'], 'text-blue-400'],
        ['VMs Ativas', $d['vms_ativas'], 'text-primary-400'],
        ['Propostas Aprovadas', $d['propostas_aprovadas'], 'text-emerald-400'],
        ['Contratos S3', $d['s3_contratos'], 'text-yellow-400'],
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
        <h3 class="text-sm font-bold text-white mb-4">Distribuição de Receita</h3>
        <canvas id="partnerRevenueChart" height="200"></canvas>
    </div>
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-white mb-4">Top 5 Clientes por MRR</h3>
        <canvas id="partnerTopCustomers" height="200"></canvas>
    </div>
</div>

{{-- Tabela de Clientes --}}
<div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-700">
        <h3 class="text-sm font-bold text-white">Clientes</h3>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-800 border-b border-gray-700">
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Cliente</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">VMs</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">MRR</th>
                <th class="text-center px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($d['clientes_list'] as $c)
            <tr class="border-b border-gray-700/50 hover:bg-gray-800/30">
                <td class="px-4 py-2 text-white font-medium">{{ $c->trade_name ?? $c->name }}</td>
                <td class="px-4 py-2 text-gray-400 text-right">{{ $c->vms_count ?? 0 }}</td>
                <td class="px-4 py-2 text-emerald-400 font-bold text-right">R$ {{ number_format($c->mrr ?? 0,2,',','.') }}</td>
                <td class="px-4 py-2 text-center">
                    <span class="text-xs {{ $c->is_active ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $c->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-3 text-gray-500 text-center text-xs">Nenhum cliente encontrado</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const tickColor = 'rgba(255,255,255,0.4)';
    const gridColor = 'rgba(255,255,255,0.07)';

    const revCtx = document.getElementById('partnerRevenueChart');
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

    const topCtx = document.getElementById('partnerTopCustomers');
    if (topCtx) {
        const topData = @json($d['top_customers']->map(fn($c) => ['name' => $c->trade_name ?? $c->name, 'mrr' => round($c->mrr ?? 0, 2)]));
        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: topData.map(c => c.name),
                datasets: [{
                    label: 'MRR',
                    data: topData.map(c => c.mrr),
                    backgroundColor: 'rgba(99,102,241,0.7)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
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
})();
</script>
</x-filament-panels::page>
