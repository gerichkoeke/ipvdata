<?php

namespace App\Filament\Distributor\Resources\CustomerResource\Pages;

use App\Filament\Distributor\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerBackupContract;
use App\Models\CustomerS3Contract;
use App\Models\Project;
use Filament\Resources\Pages\Page;

class CustomerInfra extends Page
{
    protected static string $resource = CustomerResource::class;
    protected static string $view     = 'filament.distributor.resources.customer-resource.pages.customer-infra';

    public Customer $record;

    // ── Modais ───────────────────────────────────────────────────
    public bool $modalEscolha        = false;
    public bool $modalRede           = false;
    public bool $modalEditarRede     = false;
    public bool $modalVm             = false;
    public bool $modalEditarVm       = false;
    public bool $modalExcluirVm      = false;
    public bool $modalExcluirProjeto = false;
    public bool $modalS3             = false;
    public bool $modalEditarS3       = false;
    public bool $modalExcluirS3      = false;
    public bool $modalBackup         = false;
    public bool $modalEditarBackup   = false;
    public bool $modalExcluirBackup  = false;
    public bool $modalProposta       = false;

    // ── Pricing Modal ─────────────────────────────────────────────
    public bool    $showPricingModal  = false;
    public ?int    $selectedProjectId = null;
    public array   $pricingData       = [];
    public float   $partnerCommission = 0;
    public float   $globalDiscount    = 0;
    public array   $discounts         = [];

    // ── Proposta ─────────────────────────────────────────────────
    public array  $proposta_vm_ids       = [];
    public array  $proposta_s3_ids       = [];
    public array  $proposta_backup_ids   = [];
    public bool   $proposta_incluir_rede = true;
    public string $proposta_titulo       = '';
    public string $proposta_validade     = '';
    public string $proposta_notas        = '';
    public float  $proposta_desconto     = 0;

    // ── IDs ativos ───────────────────────────────────────────────
    public ?int $activeVmId      = null;
    public ?int $activeProjectId = null;
    public ?int $activeS3Id      = null;
    public ?int $activeBackupId  = null;

    // ── Form: Rede ───────────────────────────────────────────────
    public ?int $form_network_type_id     = null;
    public ?int $form_bandwidth_option_id = null;
    public int  $form_extra_public_ips    = 0;

    // ── Form: VM ─────────────────────��───────────────────────────
    public ?int   $form_os_family_id           = null;
    public ?int   $form_os_distribution_id     = null;
    public string $form_vm_name                = '';
    public string $form_vm_description         = '';
    public int    $form_cpu_cores              = 2;
    public int    $form_ram_gb                 = 4;
    public int    $form_disk_os_gb             = 80;
    public ?int   $form_disk_os_type_id        = null;
    public bool   $form_has_additional_disks   = false;
    public array  $form_additional_disks       = [];
    public bool   $form_has_remote_desktop     = false;
    public ?int   $form_remote_desktop_type_id = null;
    public ?int   $form_rds_license_mode_id    = null;
    public int    $form_rds_license_qty        = 5;
    public bool   $form_has_endpoint           = false;
    public ?int   $form_endpoint_security_id   = null;
    public bool   $form_has_backup_vm          = false;
    public ?int   $form_backup_retention_id    = null;
    public ?int   $form_backup_software_id     = null;
    public int    $wizardStep                  = 1;

    // ── Form: S3 ────────────────────────────────────────────���────
    public int    $form_s3_storage_gb = 100;
    public string $form_s3_notes      = '';

    // ── Form: Backup ─────────────────────────────────────────────
    public ?string $form_bkp_network         = null;
    public int     $form_bkp_machines        = 1;
    public int     $form_bkp_disk_gb         = 0;
    public array   $form_bkp_machines_detail = [];
    public ?int    $form_bkp_retention_id    = null;
    public ?int    $form_bkp_bandwidth_id    = null;
    public ?int    $form_bkp_software_id     = null;

    public function getTitle(): string
    {
        return ($this->record->trade_name ?? $this->record->name) . ' — Infraestrutura';
    }

    public function getBreadcrumbs(): array
    {
        return [
            CustomerResource::getUrl()                                          => 'Clientes',
            CustomerResource::getUrl('dashboard', ['record' => $this->record]) => $this->record->trade_name ?? $this->record->name,
            '#'                                                                  => 'Infraestrutura',
        ];
    }

    protected function getHeaderActions(): array { return []; }

    // ── Getters ──────────────────────────────────────────────────

    public function getSelectedProject(): ?Project
    {
        if (!$this->selectedProjectId) return null;
        return Project::with(['vms', 'networkType', 'bandwidthOption'])
            ->find($this->selectedProjectId);
    }

    public function getInfraData(): array
    {
        $projects = $this->record->projects()
            ->with(['vms.osDistribution', 'vms.diskOsType', 'vms.additionalDisks.diskType',
                    'vms.endpointSecurity', 'networkType', 'bandwidthOption'])
            ->orderByDesc('status')
            ->get();

        $allVms          = $projects->flatMap->vms->where('status', '!=', 'cancelled');
        $s3Contracts     = CustomerS3Contract::where('customer_id', $this->record->id)->get();
        $backupContracts = CustomerBackupContract::with(['networkType', 'retention', 'software'])
            ->where('customer_id', $this->record->id)->get();

        $mrrVms     = $projects->where('status', 'active')->sum('monthly_value');
        $mrrS3      = $s3Contracts->where('status', 'active')->sum('monthly_value');
        $mrrBackups = $backupContracts->where('status', 'active')->sum('monthly_value');

        return [
            'projects'         => $projects,
            'allVms'           => $allVms,
            'rede'             => $projects->where('network_configured', true)->first(),
            's3_contracts'     => $s3Contracts,
            'backup_contracts' => $backupContracts,
            'vcpu_total'       => $allVms->sum('cpu_cores'),
            'ram_total'        => $allVms->sum('ram_gb'),
            'disk_total'       => $allVms->sum('disk_os_gb') + $allVms->flatMap->additionalDisks->sum('size_gb'),
            'vms_ativas'       => $allVms->where('status', 'active')->count(),
            'mrr_vms'          => $mrrVms,
            'mrr_s3'           => $mrrS3,
            'mrr_backups'      => $mrrBackups,
            'mrr_total'        => $mrrVms + $mrrS3 + $mrrBackups,
        ];
    }

    public function getSelects(): array
    {
        return [
            'network_types'     => [],
            'bandwidth_options' => [],
            'os_families'       => [],
            'disk_types'        => [],
            'remote_types'      => [],
            'endpoint_options'  => [],
            'retention_options' => [],
            'backup_sw_options' => [],
        ];
    }

    public function getOsDistribuicoes(): array { return []; }
    public function getRdsModes(): array { return []; }
    public function showBandwidth(): bool { return false; }
    public function temRede(): bool
    {
        return $this->record->projects()->where('network_configured', true)->exists();
    }

    // ── Fechar modais ────────────────────────────────────────────
    public function fecharModais(): void
    {
        $this->modalEscolha        = false;
        $this->modalRede           = false;
        $this->modalEditarRede     = false;
        $this->modalVm             = false;
        $this->modalEditarVm       = false;
        $this->modalExcluirVm      = false;
        $this->modalS3             = false;
        $this->modalEditarS3       = false;
        $this->modalExcluirS3      = false;
        $this->modalBackup         = false;
        $this->modalEditarBackup   = false;
        $this->modalExcluirBackup  = false;
        $this->modalExcluirProjeto = false;
        $this->showPricingModal    = false;
    }

    // ── Pricing modal (read-only) ────────────────────────────────
    public function abrirPricing(int $projectId): void
    {
        $this->selectedProjectId = $projectId;
        $this->showPricingModal  = true;
    }

    public function closePricingModal(): void { $this->showPricingModal = false; }
    public function applyCommission(): void {}
    public function applyGlobalDiscount(): void {}
    public function applyItemDiscounts(): void {}
    public function generatePdf(): void {}

    // ── No-op: distribuidor não edita infra ──────────────────────
    public function abrirEscolha(): void {}
    public function escolherModulo(string $modulo): void {}
    public function abrirEditarVm(int $vmId): void {}
    public function abrirExcluirVm(int $vmId): void {}
    public function abrirEditarRede(): void {}
    public function abrirEditarS3(int $id): void {}
    public function abrirExcluirS3(int $id): void {}
    public function abrirEditarBackup(int $id): void {}
    public function abrirExcluirBackup(int $id): void {}
    public function abrirProposta(): void {}
    public function gerarProposta(): void {}
    public function salvarRede(): void {}
    public function salvarApenasRede(): void {}
    public function salvarVm(): void {}
    public function salvarEditarVm(): void {}
    public function confirmarExcluirVm(): void {}
    public function salvarS3(): void {}
    public function salvarEditarS3(): void {}
    public function confirmarExcluirS3(): void {}
    public function salvarBackup(): void {}
    public function salvarEditarBackup(): void {}
    public function confirmarExcluirBackup(): void {}
    public function adicionarDisco(): void {}
    public function removerDisco(int $idx): void {}
    public function adicionarMaquinaBackup(): void {}
    public function removerMaquinaBackup(int $idx): void {}
    public function wizardNext(): void {}
    public function wizardPrev(): void {}
    public function wizardIr(int $step): void {}
    public function updatedFormNetworkTypeId(): void {}
    public function updatedFormBkpMachinesDetail(): void {}
    public function viewProjectPricing(int $projectId): void { $this->abrirPricing($projectId); }
}
