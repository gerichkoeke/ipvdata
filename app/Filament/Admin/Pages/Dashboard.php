<?php

namespace App\Filament\Admin\Pages;

use App\Models\Customer;
use App\Models\CustomerBackupContract;
use App\Models\CustomerS3Contract;
use App\Models\Distributor;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectVm;
use App\Models\Proposal;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Dashboard';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.admin.pages.dashboard';

    public function getDashboardData(): array
    {
        $mrrVms    = Project::where('status', 'active')->sum('monthly_value');
        $mrrS3     = CustomerS3Contract::selectRaw('SUM(size_gb * price_per_gb) as total')->value('total') ?? 0;
        $mrrBackup = CustomerBackupContract::sum('monthly_value');
        $mrrTotal  = $mrrVms + $mrrS3 + $mrrBackup;

        $mrrChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $value = Project::where('status', 'active')
                ->whereYear('created_at', '<=', $month->year)
                ->whereMonth('created_at', '<=', $month->month)
                ->sum('monthly_value');
            $mrrChart[] = ['label' => $month->format('M/y'), 'value' => round((float)$value, 2)];
        }

        $topPartners = Partner::withSum(['projects as mrr' => fn($q) => $q->where('status', 'active')], 'monthly_value')
            ->withCount('customers')
            ->orderByDesc('mrr')
            ->limit(5)
            ->get();

        return [
            'mrr_total'             => $mrrTotal,
            'mrr_vms'               => $mrrVms,
            'mrr_s3'                => $mrrS3,
            'mrr_backup'            => $mrrBackup,
            'parceiros_ativos'      => Partner::where('is_active', true)->count(),
            'clientes_ativos'       => Customer::where('is_active', true)->count(),
            'vms_ativas'            => ProjectVm::whereHas('project', fn($q) => $q->where('status', 'active'))->count(),
            'distribuidores_ativos' => Distributor::where('is_active', true)->count(),
            'propostas_aprovadas'   => Proposal::where('status', 'approved')->whereMonth('created_at', now()->month)->count(),
            'mrr_chart'             => $mrrChart,
            'top_partners'          => $topPartners,
            'ultimas_propostas'     => Proposal::with(['customer', 'partner'])->latest()->limit(5)->get(),
            'ultimas_vms'           => ProjectVm::with(['project.customer', 'project.partner', 'osDistribution'])->latest()->limit(5)->get(),
        ];
    }
}
