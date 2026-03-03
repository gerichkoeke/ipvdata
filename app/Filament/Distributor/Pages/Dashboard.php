<?php

namespace App\Filament\Distributor\Pages;

use App\Models\Customer;
use App\Models\Distributor;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectVm;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?int    $navigationSort = 1;
    protected static string  $view = 'filament.distributor.pages.dashboard';

    public function getDashboardData(): array
    {
        $user        = auth()->user();
        $distributor = Distributor::find($user->distributor_id);
        $partnerIds  = Partner::where('distributor_id', $distributor?->id)->pluck('id');

        $mrrTotal = Project::whereIn('partner_id', $partnerIds)
            ->where('status', 'active')->sum('monthly_value');

        $comissao = $mrrTotal * (($distributor?->commission_pct ?? 0) / 100);

        $topPartners = Partner::whereIn('id', $partnerIds)
            ->withSum(['projects as mrr' => fn($q) => $q->where('status', 'active')], 'monthly_value')
            ->withCount('customers')
            ->orderByDesc('mrr')
            ->get();

        // Evolução de clientes ativos últimos 6 meses
        $clientChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Customer::whereIn('partner_id', $partnerIds)
                ->where('is_active', true)
                ->where('created_at', '<=', $month->copy()->endOfMonth())
                ->count();
            $clientChart[] = ['label' => $month->format('M/y'), 'value' => $count];
        }

        return [
            'mrr_total'        => $mrrTotal,
            'comissao'         => $comissao,
            'parceiros'        => $topPartners->count(),
            'parceiros_ativos' => Partner::whereIn('id', $partnerIds)->where('is_active', true)->count(),
            'clientes_total'   => Customer::whereIn('partner_id', $partnerIds)->count(),
            'vms_ativas'       => ProjectVm::whereHas('project', fn($q) => $q->whereIn('partner_id', $partnerIds)->where('status', 'active'))->count(),
            'top_partners'     => $topPartners,
            'distributor'      => $distributor,
            'currency'         => $distributor?->currency ?? 'BRL',
            'client_chart'     => $clientChart,
        ];
    }
}
