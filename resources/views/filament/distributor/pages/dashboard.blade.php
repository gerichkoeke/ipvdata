<x-filament-panels::page>
@php
$d = $this->getDashboardData();
$symbol = match($d['currency']) { 'USD' => 'US$', 'PYG' => '₲', default => 'R$' };
@endphp

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="col-span-2 rounded-xl border border-gray-700 bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">MRR da Rede</p>
        <p class="text-2xl font-bold text-emerald-400">{{ $symbol }} {{ number_format($d['mrr_total'],2,',','.') }}</p>
        <p class="text-[10px] text-gray-500 mt-1">Soma dos projetos ativos dos parceiros</p>
    </div>
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Comissão Estimada</p>
        <p class="text-2xl font-bold text-yellow-400">{{ $symbol }} {{ number_format($d['comissao'],2,',','.') }}</p>
        <p class="text-[10px] text-gray-500 mt-1">{{ $d['distributor']?->commission_pct ?? 0 }}% do MRR</p>
    </div>
    @foreach([
        ['Parceiros', $d['parceiros'] . ' total / ' . $d['parceiros_ativos'] . ' ativos', $d['parceiros_ativos'], 'text-blue-400'],
        ['Clientes na Rede', '', $d['clientes_total'], 'text-indigo-400'],
        ['VMs Ativas', '', $d['vms_ativas'], 'text-primary-400'],
    ] as [$label, $sub, $value, $color])
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ $label }}</p>
        <p class="text-2xl font-bold {{ $color }}">{{ $value }}</p>
        @if($sub)<p class="text-[10px] text-gray-500 mt-1">{{ $sub }}</p>@endif
    </div>
    @endforeach
</div>

{{-- Gráficos --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-white mb-4">Top 5 Parceiros por MRR</h3>
        <div style="position:relative;height:260px;">
            <canvas id="distPartnersChart"></canvas>
        </div>
    </div>
    <div class="rounded-xl border border-gray-700 bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-white mb-4">Evolução de Clientes — últimos 6 meses</h3>
        <div style="position:relative;height:260px;">
            <canvas id="distClientsChart"></canvas>
        </div>
    </div>
</div>

{{-- Tabela de Parceiros --}}
<div class="rounded-xl border border-gray-700 bg-gray-900 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-700">
        <h3 class="text-sm font-bold text-white">Parceiros da Rede</h3>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-800 border-b border-gray-700">
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Parceiro</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Clientes</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">MRR</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Comissão</th>
                <th class="text-center px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($d['top_partners'] as $p)
            @php $commP = round(($p->mrr ?? 0) * (($d['distributor']?->commission_pct ?? 0) / 100), 2); @endphp
            <tr class="border-b border-gray-700/50 hover:bg-gray-800/30">
                <td class="px-4 py-2 text-white font-medium">{{ $p->trade_name ?? $p->company_name }}</td>
                <td class="px-4 py-2 text-gray-400 text-right">{{ $p->customers_count }}</td>
                <td class="px-4 py-2 text-emerald-400 font-bold text-right">{{ $symbol }} {{ number_format($p->mrr ?? 0,2,',','.') }}</td>
                <td class="px-4 py-2 text-yellow-400 font-bold text-right">{{ $symbol }} {{ number_format($commP,2,',','.') }}</td>
                <td class="px-4 py-2 text-center">
                    <span class="text-xs {{ $p->is_active ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $p->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-3 text-gray-500 text-center text-xs">Nenhum parceiro encontrado</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const gridColor = 'rgba(255,255,255,0.07)';
    const tickColor = 'rgba(255,255,255,0.4)';
    const topPartners = @json($d['top_partners']->map(fn($p) => ['name' => $p->trade_name ?? $p->company_name, 'mrr' => round($p->mrr ?? 0, 2)]));
    const clientData  = @json($d['client_chart']);

    const partnersCtx = document.getElementById('distPartnersChart');
    if (partnersCtx) {
        new Chart(partnersCtx, {
            type: 'bar',
            data: {
                labels: topPartners.map(p => p.name),
                datasets: [{
                    label: 'MRR',
                    data: topPartners.map(p => p.mrr),
                    backgroundColor: 'rgba(59,130,246,0.7)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor } }
                }
            }
        });
    }

    const clientsCtx = document.getElementById('distClientsChart');
    if (clientsCtx) {
        new Chart(clientsCtx, {
            type: 'line',
            data: {
                labels: clientData.map(c => c.label),
                datasets: [{
                    label: 'Clientes Ativos',
                    data: clientData.map(c => c.value),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor, stepSize: 1 } }
                }
            }
        });
    }
})();
</script>
</x-filament-panels::page>
