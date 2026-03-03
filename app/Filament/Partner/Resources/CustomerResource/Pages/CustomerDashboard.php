<?php

namespace App\Filament\Partner\Resources\CustomerResource\Pages;

use App\Filament\Partner\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerBackupContract;
use App\Models\CustomerS3Contract;
use Filament\Resources\Pages\Page;

class CustomerDashboard extends Page
{
    protected static string $resource = CustomerResource::class;
    protected static string $view     = 'filament.partner.resources.customer-resource.pages.customer-dashboard';

    public Customer $record;

    public function getTitle(): string
    {
        return $this->record->trade_name ?? $this->record->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            CustomerResource::getUrl() => 'Clientes',
            '#'                        => $this->record->trade_name ?? $this->record->name,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getDashboardData(): array
    {
        $record   = $this->record->load(['projects.vms.additionalDisks', 'proposals']);
        $projects = $record->projects;
        $vms      = $projects->flatMap->vms->where('status', '!=', 'cancelled');

        $s3      = CustomerS3Contract::where('customer_id', $record->id)->get();
        $backups = CustomerBackupContract::where('customer_id', $record->id)->get();

        $mrrVms     = $projects->where('status', 'active')->sum('monthly_value');
        $mrrS3      = $s3->sum(fn ($c) => $c->price_per_gb * $c->size_gb);
        $mrrBackups = $backups->sum('monthly_value');

        return [
            'tem_infra'          => $projects->isNotEmpty() || $s3->isNotEmpty() || $backups->isNotEmpty(),
            'projetos_ativos'    => $projects->where('status', 'active')->count(),
            'vms_ativas'         => $vms->where('status', 'active')->count(),
            'vms_total'          => $vms->count(),
            'vcpu_total'         => $vms->sum('cpu_cores'),
            'ram_total'          => $vms->sum('ram_gb'),
            'disk_total'         => $vms->sum('disk_os_gb') + $vms->flatMap->additionalDisks->sum('size_gb'),
            's3_contratos'       => $s3,
            's3_total_gb'        => $s3->sum('size_gb'),
            'backup_contratos'   => $backups,
            'backup_maquinas'    => $backups->sum('machines'),
            'proposals_total'    => $record->proposals->count(),
            'proposals_approved' => $record->proposals->where('status', 'approved')->count(),
            'mrr_vms'            => $mrrVms,
            'mrr_s3'             => $mrrS3,
            'mrr_backups'        => $mrrBackups,
            'mrr_total'          => $mrrVms + $mrrS3 + $mrrBackups,
            'rede'               => $projects->where('network_configured', true)->first(),
        ];
    }

    public function getInfraUrl(): string
    {
        return CustomerInfra::getUrl(['record' => $this->record]);
    }
}
