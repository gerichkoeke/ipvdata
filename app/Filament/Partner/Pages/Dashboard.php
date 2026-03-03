<?php

namespace App\Filament\Partner\Pages;

use App\Models\Customer;
use App\Models\CustomerBackupContract;
use App\Models\CustomerS3Contract;
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
    protected static string  $view            = 'filament.partner.pages.dashboard';

    public function getDashboardData(): array
    {
        $user      = filament()->auth()->user();
        $partnerId = $user?->partner_id;
        $partner   = $user?->partner;

        $mrrVms = Project::where('partner_id', $partnerId)
            ->where('status', 'active')->sum('monthly_value');

        $customerIds = Customer::where('partner_id', $partnerId)->pluck('id');

        $mrrS3 = CustomerS3Contract::whereIn('customer_id', $customerIds)
            ->selectRaw('SUM(size_gb * price_per_gb) as total')->value('total') ?? 0;

        $mrrBackup = CustomerBackupContract::whereIn('customer_id', $customerIds)
            ->sum('monthly_value');

        $mrrTotal = $mrrVms + $mrrS3 + $mrrBackup;

        $topCustomers = Customer::where('partner_id', $partnerId)
            ->withSum(['projects as mrr' => fn($q) => $q->where('status', 'active')], 'monthly_value')
            ->orderByDesc('mrr')
            ->limit(5)
            ->get();

        return [
            'mrr_total'         => $mrrTotal,
            'mrr_vms'           => $mrrVms,
            'mrr_s3'            => $mrrS3,
            'mrr_backup'        => $mrrBackup,
            'clientes_ativos'   => Customer::where('partner_id', $partnerId)->where('is_active', true)->count(),
            'vms_ativas'        => ProjectVm::whereHas('project', fn($q) => $q->where('partner_id', $partnerId)->where('status', 'active'))->count(),
            'propostas_aprovadas' => Proposal::where('partner_id', $partnerId)->where('status', 'approved')->whereMonth('created_at', now()->month)->count(),
            's3_contratos'      => CustomerS3Contract::whereIn('customer_id', $customerIds)->count(),
            'partner'           => $partner,
            'top_customers'     => $topCustomers,
            'clientes_list'     => Customer::where('partner_id', $partnerId)
                ->withSum(['projects as mrr' => fn($q) => $q->where('status', 'active')], 'monthly_value')
                ->withCount(['projects as vms_count' => fn($q) => $q->where('status', 'active')])
                ->orderByDesc('mrr')
                ->limit(10)
                ->get(),
        ];
    }
}
