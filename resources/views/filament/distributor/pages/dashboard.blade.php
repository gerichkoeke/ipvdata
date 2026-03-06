<x-filament-panels::page>
@php
$d = $this->getDashboardData();
$symbol = match($d['currency']) { 'USD' => 'US$', 'PYG' => '₲', default => 'R$' };
$dashboardI18n = __('app.dashboard.distributor');
@endphp

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ $dashboardI18n['network_mrr'] }}</p>
        <p class="text-2xl font-bold text-emerald-400">{{ $symbol }} {{ number_format($d['mrr_total'],2,',','.') }}</p>
        <p class="text-[10px] text-gray-500 mt-1">{{ $dashboardI18n['network_mrr_hint'] }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ $dashboardI18n['estimated_commission'] }}</p>
        <p class="text-2xl font-bold text-yellow-400">{{ $symbol }} {{ number_format($d['comissao'],2,',','.') }}</p>
        <p class="text-[10px] text-gray-500 mt-1">{{ $d['distributor']?->commission_pct ?? 0 }}% {{ $dashboardI18n['of_mrr'] }}</p>
    </div>
    @foreach([
        [$dashboardI18n['partners'], $d['parceiros'] . ' ' . $dashboardI18n['total'] . ' / ' . $d['parceiros_ativos'] . ' ' . $dashboardI18n['active'], $d['parceiros_ativos'], 'text-blue-400'],
        [$dashboardI18n['network_customers'], '', $d['clientes_total'], 'text-indigo-400'],
        [$dashboardI18n['active_vms'], '', $d['vms_ativas'], 'text-primary-400'],
    ] as [$label, $sub, $value, $color])
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ $label }}</p>
        <p class="text-2xl font-bold {{ $color }}">{{ $value }}</p>
        @if($sub)<p class="text-[10px] text-gray-500 mt-1">{{ $sub }}</p>@endif
    </div>
    @endforeach
</div>

{{-- Gráficos --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ $dashboardI18n['top_partners_mrr'] }}</h3>
        <div style="position:relative;height:260px;">
            <canvas id="distPartnersChart"></canvas>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ $dashboardI18n['customers_evolution_6_months'] }}</h3>
        <div style="position:relative;height:260px;">
            <canvas id="distClientsChart"></canvas>
        </div>
    </div>
</div>

{{-- Tabela de Parceiros --}}
<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $dashboardI18n['network_partners'] }}</h3>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <th class="text-left px-4 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $dashboardI18n['partner'] }}</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $dashboardI18n['customers'] }}</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">MRR</th>
                <th class="text-right px-4 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $dashboardI18n['commission'] }}</th>
                <th class="text-center px-4 py-2 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">{{ __('app.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($d['top_partners'] as $p)
            @php $commP = round(($p->mrr ?? 0) * (($d['distributor']?->commission_pct ?? 0) / 100), 2); @endphp
            <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-800/30">
                <td class="px-4 py-2 text-gray-900 dark:text-white font-medium">{{ $p->trade_name ?? $p->company_name }}</td>
                <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-right">{{ $p->customers_count }}</td>
                <td class="px-4 py-2 text-emerald-400 font-bold text-right">{{ $symbol }} {{ number_format($p->mrr ?? 0,2,',','.') }}</td>
                <td class="px-4 py-2 text-yellow-400 font-bold text-right">{{ $symbol }} {{ number_format($commP,2,',','.') }}</td>
                <td class="px-4 py-2 text-center">
                    <span class="text-xs {{ $p->is_active ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $p->is_active ? __('app.active') : __('app.inactive') }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-3 text-gray-500 text-center text-xs">{{ $dashboardI18n['no_partners_found'] }}</td></tr>
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
