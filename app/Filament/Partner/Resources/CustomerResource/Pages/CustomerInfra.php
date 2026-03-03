<?php

namespace App\Filament\Partner\Resources\CustomerResource\Pages;

use App\Filament\Partner\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Project;
use App\Services\ProjectPricingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CustomerInfra extends Page
{
    protected static string $resource = CustomerResource::class;
    protected static string $view = 'filament.partner.resources.customer-resource.pages.customer-infra';
    
    public Customer $customer;
    public ?Project $selectedProject = null;
    public ?array $pricingData = null;
    public bool $showPricingModal = false;
    public array $discounts = [];
    public ?float $partnerCommission = null;
    public ?float $globalDiscount = null;

    public function mount(int $record): void
    {
        $this->customer = Customer::with(['projects.partner'])->findOrFail($record);
        
        // Verificar se o cliente pertence ao parceiro logado
        $partner = Auth::user()->partner;
        if (!$partner || $this->customer->partner_id !== $partner->id) {
            abort(403);
        }
    }

    public function viewProjectPricing(int $projectId): void
    {
        $this->selectedProject = Project::with([
            'vms.disks.diskType',
            'vms.osDistribution',
            'vms.endpointSecurity',
            'vms.backupRetention',
            's3Storage',
            'backupStandalone.backupSoftware',
            'backupStandalone.retention',
            'networkType',
            'bandwidthOption',
            'firewallOption',
            'lanType',
            'partner',
        ])->findOrFail($projectId);

        // Inicializar valores atuais
        $this->partnerCommission = $this->selectedProject->partner_commission_percentage;
        $this->globalDiscount = $this->selectedProject->global_discount_amount;
        
        // Inicializar descontos individuais
        $this->discounts = [
            'network' => $this->selectedProject->network_discount_amount,
        ];

        foreach ($this->selectedProject->vms as $vm) {
            $this->discounts['vm_' . $vm->id] = $vm->discount_amount;
        }

        foreach ($this->selectedProject->s3Storage as $s3) {
            $this->discounts['s3_' . $s3->id] = $s3->discount_amount;
        }

        foreach ($this->selectedProject->backupStandalone as $backup) {
            $this->discounts['backup_' . $backup->id] = $backup->discount_amount;
        }

        $pricingService = new ProjectPricingService();
        $this->pricingData = $pricingService->getDetailedPricing($this->selectedProject);
        $this->showPricingModal = true;
    }

    public function applyCommission(): void
    {
        if (!$this->selectedProject->hasVariableCommission()) {
            Notification::make()
                ->warning()
                ->title('Ação não permitida')
                ->body('Este parceiro não possui comissão variável.')
                ->send();
            return;
        }

        $this->validate([
            'partnerCommission' => 'required|numeric|min:0|max:100',
        ]);

        $this->selectedProject->update([
            'partner_commission_percentage' => $this->partnerCommission,
        ]);

        Notification::make()
            ->success()
            ->title('Comissão aplicada')
            ->body('Porcentagem de comissão atualizada com sucesso.')
            ->send();

        $this->refreshPricing();
    }

    public function applyGlobalDiscount(): void
    {
        $this->validate([
            'globalDiscount' => 'required|numeric|min:0',
        ]);

        $this->selectedProject->update([
            'global_discount_amount' => $this->globalDiscount,
            'global_discount_currency' => $this->selectedProject->currency,
        ]);

        Notification::make()
            ->success()
            ->title('Desconto global aplicado')
            ->body('Desconto aplicado ao projeto inteiro.')
            ->send();

        $this->refreshPricing();
    }

    public function applyItemDiscounts(): void
    {
        // Aplicar desconto na rede
        if (isset($this->discounts['network'])) {
            $this->selectedProject->update([
                'network_discount_amount' => $this->discounts['network'] ?? 0,
            ]);
        }

        // Aplicar descontos nas VMs
        foreach ($this->selectedProject->vms as $vm) {
            $key = 'vm_' . $vm->id;
            if (isset($this->discounts[$key])) {
                $vm->update([
                    'discount_amount' => $this->discounts[$key] ?? 0,
                ]);
            }
        }

        // Aplicar descontos no S3
        foreach ($this->selectedProject->s3Storage as $s3) {
            $key = 's3_' . $s3->id;
            if (isset($this->discounts[$key])) {
                $s3->update([
                    'discount_amount' => $this->discounts[$key] ?? 0,
                ]);
            }
        }

        // Aplicar descontos no Backup
        foreach ($this->selectedProject->backupStandalone as $backup) {
            $key = 'backup_' . $backup->id;
            if (isset($this->discounts[$key])) {
                $backup->update([
                    'discount_amount' => $this->discounts[$key] ?? 0,
                ]);
            }
        }

        Notification::make()
            ->success()
            ->title('Descontos aplicados')
            ->body('Descontos individuais aplicados aos itens.')
            ->send();

        $this->refreshPricing();
    }

    public function generatePdf(): void
    {
        if (!$this->selectedProject) {
            return;
        }

        $url = route('proposals.print', [
            'project' => $this->selectedProject->id,
            'customer' => $this->customer->id,
        ]);

        $this->redirect($url, navigate: false);
    }

    protected function refreshPricing(): void
    {
        $this->selectedProject->refresh();
        $pricingService = new ProjectPricingService();
        $this->pricingData = $pricingService->getDetailedPricing($this->selectedProject);
    }

    public function closePricingModal(): void
    {
        $this->showPricingModal = false;
        $this->selectedProject = null;
        $this->pricingData = null;
        $this->discounts = [];
        $this->partnerCommission = null;
        $this->globalDiscount = null;
    }

    // ─── Propriedades da View ─────────────────────────────────────
    public bool   $modalEscolha        = false;
    public bool   $modalRede           = false;
    public bool   $modalEditarRede     = false;
    public bool   $modalVm             = false;
    public bool   $modalEditarVm       = false;
    public bool   $modalExcluirVm      = false;
    public bool   $modalExcluirProjeto = false;
    public bool   $modalExcluirRede    = false;
    public bool   $modalS3             = false;
    public bool   $modalEditarS3       = false;
    public bool   $modalExcluirS3      = false;
    public bool   $modalBackup         = false;
    public bool   $modalEditarBackup   = false;
    public bool   $modalExcluirBackup  = false;
    public bool   $modalProposta       = false;
    public ?int   $activeVmId          = null;
    public ?int   $activeProjectId     = null;
    public ?int   $activeS3Id          = null;
    public ?int   $activeBackupId      = null;
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
    public int    $form_s3_storage_gb          = 100;
    public string $form_s3_notes               = '';
    public ?string $form_bkp_network           = null;
    public int    $form_bkp_machines           = 1;
    public int    $form_bkp_disk_gb            = 0;
    public array  $form_bkp_machines_detail    = [];
    public ?int   $form_bkp_retention_id       = null;
    public ?int   $form_bkp_bandwidth_id       = null;
    public ?int   $form_bkp_software_id        = null;
    public ?int   $form_network_type_id        = null;
    public ?int   $form_bandwidth_option_id    = null;
    public int    $form_extra_public_ips       = 0;
    public array  $proposta_vm_ids             = [];
    public array  $proposta_s3_ids             = [];
    public array  $proposta_backup_ids         = [];
    public bool   $proposta_incluir_rede       = true;
    public string $proposta_titulo             = '';
    public string $proposta_validade           = '';
    public string $proposta_notas              = '';
    public float  $proposta_desconto           = 0;

    // ─── Dados Infra ──────────────────────────────────────────────
    public function getInfraData(): array
    {
        $projects = $this->customer->projects()
            ->with(['vms.osDistribution', 'vms.diskOsType', 'vms.additionalDisks.diskType',
                    'vms.endpointSecurity', 'networkType', 'bandwidthOption'])
            ->orderByDesc('status')
            ->get();

        $allVms          = $projects->flatMap->vms->where('status', '!=', 'cancelled');
        $s3Contracts     = \App\Models\CustomerS3Contract::where('customer_id', $this->customer->id)->get();
        $backupContracts = \App\Models\CustomerBackupContract::with(['networkType', 'retention', 'software'])
            ->where('customer_id', $this->customer->id)->get();

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
            'network_types'     => \App\Models\NetworkType::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'bandwidth_options' => \App\Models\BandwidthOption::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'os_families'       => \App\Models\OsFamily::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'disk_types'        => \App\Models\DiskType::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'remote_types'      => \App\Models\RemoteDesktopType::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'endpoint_options'  => \App\Models\EndpointSecurityOption::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'retention_options' => \App\Models\BackupRetentionOption::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
            'backup_sw_options' => \App\Models\BackupSoftwareOption::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->toArray(),
        ];
    }

    public function getOsDistribuicoes(): array
    {
        $q = \App\Models\OsDistribution::where('is_active', true)->orderBy('sort_order');
        if ($this->form_os_family_id) $q->where('os_family_id', $this->form_os_family_id);
        return $q->pluck('name', 'id')->toArray();
    }

    public function getRdsModes(): array
    {
        if (!$this->form_remote_desktop_type_id) return [];
        $t = \App\Models\RemoteDesktopType::find($this->form_remote_desktop_type_id);
        if (!$t || !$t->has_license_modes) return [];
        return \App\Models\RdsLicenseMode::where('remote_desktop_type_id', $this->form_remote_desktop_type_id)
            ->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public function showBandwidth(): bool
    {
        if (!$this->form_network_type_id) return true;
        $nt = \App\Models\NetworkType::find($this->form_network_type_id);
        return !($nt && $nt->slug === 'lan-to-lan');
    }

    public function temRede(): bool
    {
        return $this->customer->projects()->where('network_configured', true)->exists();
    }

    public function updatedFormNetworkTypeId(): void
    {
        $this->form_bandwidth_option_id = null;
        $this->form_extra_public_ips    = 0;
    }

    public function updatedFormBkpMachinesDetail(): void
    {
        $this->form_bkp_machines = count($this->form_bkp_machines_detail);
        $this->form_bkp_disk_gb  = array_sum(array_column($this->form_bkp_machines_detail, 'disk_gb'));
    }

    // ─── Modais ───────────────────────────────────────────────────
    public function abrirEscolha(): void { $this->modalEscolha = true; }

    public function escolherModulo(string $modulo): void
    {
        $this->modalEscolha = false;
        if ($modulo === 'vm') {
            if ($this->temRede()) {
                $this->resetFormVm();
                $this->wizardStep = 1;
                $this->modalVm    = true;
            } else {
                $this->resetFormRede();
                $this->modalRede = true;
            }
        } elseif ($modulo === 's3') {
            $this->form_s3_storage_gb = 100;
            $this->form_s3_notes      = '';
            $this->modalS3            = true;
        } elseif ($modulo === 'backup') {
            $this->form_bkp_network         = null;
            $this->form_bkp_machines        = 1;
            $this->form_bkp_disk_gb         = 100;
            $this->form_bkp_retention_id    = null;
            $this->form_bkp_bandwidth_id    = null;
            $this->form_bkp_machines_detail = [];
            $this->form_bkp_software_id     = null;
            $this->modalBackup              = true;
        }
    }

    public function abrirEditarVm(int $vmId): void
    {
        $vm = \App\Models\ProjectVm::with('additionalDisks')->find($vmId);
        if (!$vm) return;
        $this->activeVmId                  = $vmId;
        $this->form_os_family_id           = $vm->osDistribution?->os_family_id;
        $this->form_os_distribution_id     = $vm->os_distribution_id;
        $this->form_vm_name                = $vm->name;
        $this->form_vm_description         = $vm->description ?? '';
        $this->form_cpu_cores              = $vm->cpu_cores;
        $this->form_ram_gb                 = $vm->ram_gb;
        $this->form_disk_os_gb             = $vm->disk_os_gb;
        $this->form_disk_os_type_id        = $vm->disk_os_type_id;
        $this->form_has_additional_disks   = $vm->additionalDisks->isNotEmpty();
        $this->form_additional_disks       = $vm->additionalDisks->map(fn ($d) => ['disk_type_id' => $d->disk_type_id, 'size_gb' => $d->size_gb])->toArray();
        $this->form_has_remote_desktop     = !empty($vm->remote_desktop_type_id);
        $this->form_remote_desktop_type_id = $vm->remote_desktop_type_id;
        $this->form_rds_license_mode_id    = $vm->rds_license_mode_id;
        $this->form_rds_license_qty        = $vm->rds_license_qty ?: 5;
        $this->form_has_endpoint           = !empty($vm->endpoint_security_id);
        $this->form_endpoint_security_id   = $vm->endpoint_security_id;
        $this->form_has_backup_vm          = (bool) $vm->has_backup;
        $this->form_backup_retention_id    = $vm->backup_retention_id;
        $this->form_backup_software_id     = $vm->backup_software_id;
        $this->wizardStep                  = 1;
        $this->modalEditarVm               = true;
    }

    public function abrirExcluirVm(int $vmId): void
    {
        $this->activeVmId     = $vmId;
        $this->modalExcluirVm = true;
    }

    public function abrirEditarRede(): void
    {
        $projeto = $this->customer->projects()->where('network_configured', true)->first();
        if (!$projeto) return;
        $this->activeProjectId          = $projeto->id;
        $this->form_network_type_id     = $projeto->network_type_id;
        $this->form_bandwidth_option_id = $projeto->bandwidth_option_id;
        $this->form_extra_public_ips    = $projeto->extra_public_ips ?? 0;
        $this->modalEditarRede          = true;
    }

    public function abrirEditarS3(int $id): void
    {
        $s3 = \App\Models\CustomerS3Contract::find($id);
        if (!$s3) return;
        $this->activeS3Id         = $id;
        $this->form_s3_storage_gb = (int) ($s3->size_gb ?? 0);
        $this->form_s3_notes      = $s3->notes ?? '';
        $this->modalEditarS3      = true;
    }

    public function abrirExcluirS3(int $id): void
    {
        $this->activeS3Id     = $id;
        $this->modalExcluirS3 = true;
    }

    public function abrirEditarBackup(int $id): void
    {
        $bkp = \App\Models\CustomerBackupContract::find($id);
        if (!$bkp) return;
        $this->activeBackupId           = $id;
        $this->form_bkp_network         = $bkp->network_type_id ? (string) $bkp->network_type_id : 'vpn_client';
        $this->form_bkp_machines        = (int) $bkp->machines;
        $this->form_bkp_disk_gb         = (int) $bkp->total_disk_gb;
        $this->form_bkp_retention_id    = $bkp->retention_id;
        $this->form_bkp_bandwidth_id    = $bkp->bandwidth_option_id;
        $this->form_bkp_machines_detail = $bkp->machines_detail ?? [];
        $this->form_bkp_software_id     = $bkp->backup_software_id;
        $this->modalEditarBackup        = true;
    }

    public function abrirExcluirBackup(int $id): void
    {
        $this->activeBackupId     = $id;
        $this->modalExcluirBackup = true;
    }

    public function fecharModais(): void
    {
        $this->modalEscolha = $this->modalRede = $this->modalEditarRede = false;
        $this->modalVm      = $this->modalEditarVm = $this->modalExcluirVm = false;
        $this->modalS3      = $this->modalEditarS3 = $this->modalExcluirS3 = false;
        $this->modalBackup  = $this->modalEditarBackup = $this->modalExcluirBackup = false;
        $this->modalExcluirProjeto = false;
    }

    // ─── Wizard ───────────────────────────────────────────────────
    public function wizardNext(): void { $this->wizardStep++; }
    public function wizardPrev(): void { if ($this->wizardStep > 1) $this->wizardStep--; }
    public function wizardIr(int $step): void { $this->wizardStep = $step; }

    // ─── Salvar Rede ──────────────────────────────────────────────
    public function salvarRede(): void
    {
        $project = \App\Models\Project::firstOrCreate(
            [
                'customer_id' => $this->customer->id,
                'partner_id'  => auth()->user()?->partner_id,
                'name'        => 'CDV - ' . ($this->customer->trade_name ?? $this->customer->name),
            ],
            ['status' => 'active', 'monthly_value' => 0]
        );
        $extraIps = max(0, (int) $this->form_extra_public_ips);
        $project->update([
            'network_type_id'     => $this->form_network_type_id,
            'bandwidth_option_id' => $this->form_bandwidth_option_id,
            'extra_public_ips'    => $extraIps,
            'extra_ip_price'      => $extraIps * $this->getUnitPrice('public_ip'),
            'network_configured'  => true,
        ]);
        $this->recalcProject($project->id);
        $this->activeProjectId = $project->id;
        $this->modalRede       = false;
        $this->resetFormVm();
        $this->wizardStep = 1;
        $this->modalVm    = true;
        \Filament\Notifications\Notification::make()->title('Rede configurada!')->success()->send();
    }

    public function salvarApenasRede(): void
    {
        $projeto = \App\Models\Project::find($this->activeProjectId);
        if (!$projeto) return;
        $extraIps = max(0, (int) $this->form_extra_public_ips);
        $projeto->update([
            'network_type_id'     => $this->form_network_type_id,
            'bandwidth_option_id' => $this->form_bandwidth_option_id,
            'extra_public_ips'    => $extraIps,
            'extra_ip_price'      => $extraIps * $this->getUnitPrice('public_ip'),
            'network_configured'  => true,
        ]);
        $this->recalcProject($projeto->id);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('Rede atualizada')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    // ─── Salvar VM ────────────────────────────────────────────────
    public function salvarVm(): void
    {
        $project = $this->activeProjectId
            ? \App\Models\Project::find($this->activeProjectId)
            : $this->customer->projects()->where('network_configured', true)->first();
        if (!$project) {
            \Filament\Notifications\Notification::make()->title('Configure a rede primeiro')->danger()->send();
            return;
        }
        $this->persistirVm(null, $project->id);
    }

    public function salvarEditarVm(): void
    {
        $vm = \App\Models\ProjectVm::find($this->activeVmId);
        if (!$vm) return;
        $this->persistirVm($this->activeVmId, $vm->project_id);
    }

    public function confirmarExcluirVm(): void
    {
        $vm = \App\Models\ProjectVm::find($this->activeVmId);
        if (!$vm) return;
        $pid = $vm->project_id;
        $vm->additionalDisks()->delete();
        $vm->delete();
        $this->recalcProject($pid);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('VM excluída')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    // ─── Salvar S3 ────────────────────────────────────────────────
    public function salvarS3(): void
    {
        $pricePerGb = (float) (\App\Models\ResourcePricing::where('resource_type', 's3_gb')->value('price') ?? 0);
        \App\Models\CustomerS3Contract::create([
            'customer_id'  => $this->customer->id,
            'size_gb'      => (int) $this->form_s3_storage_gb,
            'price_per_gb' => $pricePerGb,
        ]);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('S3 contratado!')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    public function salvarEditarS3(): void
    {
        $s3 = \App\Models\CustomerS3Contract::find($this->activeS3Id);
        if (!$s3) return;
        $pricePerGb = (float) (\App\Models\ResourcePricing::where('resource_type', 's3_gb')->value('price') ?? $s3->price_per_gb);
        $s3->update([
            'size_gb'      => (int) $this->form_s3_storage_gb,
            'price_per_gb' => $pricePerGb,
        ]);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('S3 atualizado')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    public function confirmarExcluirS3(): void
    {
        \App\Models\CustomerS3Contract::find($this->activeS3Id)?->delete();
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('S3 removido')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    // ─── Salvar Backup ────────────────────────────────────────────
    private function resolveBackupNetwork(): array
    {
        if ($this->form_bkp_network === 'vpn_client') return ['network_type_id' => null];
        return ['network_type_id' => $this->form_bkp_network ? (int) $this->form_bkp_network : null];
    }

    private function calcBackupValor(): array
    {
        $pricePerGb     = (float) (\App\Models\ResourcePricing::where('resource_type', 'backup_gb')->value('price') ?? 0);
        $backupGb       = $this->form_bkp_disk_gb * 0.5;
        $retention      = $this->form_bkp_retention_id ? \App\Models\BackupRetentionOption::find($this->form_bkp_retention_id) : null;
        $multiplier     = $retention ? (float) $retention->price_multiplier : 1;
        $bkpSw          = $this->form_bkp_software_id ? \App\Models\BackupSoftwareOption::find($this->form_bkp_software_id) : null;
        $priceSoftware  = $bkpSw ? $bkpSw->calculateCost($this->form_bkp_machines) : 0;
        $bandwidth      = $this->form_bkp_bandwidth_id ? \App\Models\BandwidthOption::find($this->form_bkp_bandwidth_id) : null;
        $priceBandwidth = (float) ($bandwidth?->price ?? 0);
        $monthly        = ($backupGb * $pricePerGb * $multiplier) + $priceSoftware + $priceBandwidth;
        return compact('pricePerGb', 'backupGb', 'multiplier', 'priceSoftware', 'priceBandwidth', 'monthly');
    }

    public function salvarBackup(): void
    {
        $network = $this->resolveBackupNetwork();
        $calc    = $this->calcBackupValor();
        \App\Models\CustomerBackupContract::create([
            'customer_id'         => $this->customer->id,
            'type'                => $this->form_bkp_network ?? 'vpn_client',
            'machines'            => (int) $this->form_bkp_machines,
            'total_disk_gb'       => (int) $this->form_bkp_disk_gb,
            'network_type_id'     => $network['network_type_id'] ?? null,
            'bandwidth_option_id' => $this->form_bkp_bandwidth_id,
            'retention_id'        => $this->form_bkp_retention_id,
            'backup_software_id'  => $this->form_bkp_software_id,
            'machines_detail'     => $this->form_bkp_machines_detail ?: null,
            'monthly_value'       => $calc['monthly'],
        ]);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('Backup contratado!')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    public function salvarEditarBackup(): void
    {
        $bkp = \App\Models\CustomerBackupContract::find($this->activeBackupId);
        if (!$bkp) return;
        $network = $this->resolveBackupNetwork();
        $calc    = $this->calcBackupValor();
        $bkp->update([
            'machines'            => (int) $this->form_bkp_machines,
            'total_disk_gb'       => (int) $this->form_bkp_disk_gb,
            'network_type_id'     => $network['network_type_id'] ?? null,
            'bandwidth_option_id' => $this->form_bkp_bandwidth_id,
            'retention_id'        => $this->form_bkp_retention_id,
            'backup_software_id'  => $this->form_bkp_software_id,
            'machines_detail'     => $this->form_bkp_machines_detail ?: null,
            'monthly_value'       => $calc['monthly'],
        ]);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('Backup atualizado')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    public function confirmarExcluirBackup(): void
    {
        \App\Models\CustomerBackupContract::find($this->activeBackupId)?->delete();
        $this->fecharModais();
        \Filament\Notifications\Notification::make()->title('Backup removido')->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    // ─── Discos / Máquinas Backup ─────────────────────────────────
    public function adicionarMaquinaBackup(): void
    {
        $this->form_bkp_machines_detail[] = ['descricao' => '', 'disk_gb' => 100];
        $this->form_bkp_machines = count($this->form_bkp_machines_detail);
        $this->form_bkp_disk_gb  = array_sum(array_column($this->form_bkp_machines_detail, 'disk_gb'));
    }

    public function removerMaquinaBackup(int $idx): void
    {
        array_splice($this->form_bkp_machines_detail, $idx, 1);
        $this->form_bkp_machines_detail = array_values($this->form_bkp_machines_detail);
        $this->form_bkp_machines = count($this->form_bkp_machines_detail);
        $this->form_bkp_disk_gb  = array_sum(array_column($this->form_bkp_machines_detail, 'disk_gb'));
    }

    public function adicionarDisco(): void
    {
        $this->form_additional_disks[] = ['disk_type_id' => null, 'size_gb' => 100];
    }

    public function removerDisco(int $idx): void
    {
        array_splice($this->form_additional_disks, $idx, 1);
        $this->form_additional_disks = array_values($this->form_additional_disks);
    }

    // ─── Proposta ─────────────────────────────────────────────────
    public function abrirProposta(): void
    {
        $data = $this->getInfraData();
        $this->proposta_vm_ids       = $data['allVms']->pluck('id')->toArray();
        $this->proposta_s3_ids       = $data['s3_contracts']->pluck('id')->toArray();
        $this->proposta_backup_ids   = $data['backup_contracts']->pluck('id')->toArray();
        $this->proposta_incluir_rede = true;
        $this->proposta_titulo       = 'Proposta Cloud — ' . ($this->customer->trade_name ?? $this->customer->name);
        $this->proposta_validade     = now()->addDays(30)->format('Y-m-d');
        $this->proposta_notas        = '';
        $this->proposta_desconto     = 0;
        $this->modalProposta         = true;
    }

    public function gerarProposta(): void
    {
        $partner = auth()->user()?->partner;
        if (!$partner) {
            \Filament\Notifications\Notification::make()->title('Parceiro não encontrado')->danger()->send();
            return;
        }
        $data    = $this->getInfraData();
        $vms     = $data['allVms']->whereIn('id', $this->proposta_vm_ids);
        $s3s     = $data['s3_contracts']->whereIn('id', $this->proposta_s3_ids);
        $backups = $data['backup_contracts']->whereIn('id', $this->proposta_backup_ids);
        $rede    = $this->proposta_incluir_rede ? $data['rede'] : null;

        $netCost = 0;
        if ($rede) {
            $isLan   = $rede->networkType?->slug === 'lan-to-lan';
            $netCost = $isLan
                ? (float) ($rede->networkType?->price ?? 0)
                : ((float) ($rede->extra_ip_price ?? 0) + (float) ($rede->bandwidthOption?->price ?? 0));
        }

        $subtotal = $vms->sum('price_total_monthly')
            + $s3s->sum(fn ($s3) => $s3->size_gb * $s3->price_per_gb)
            + $backups->sum('monthly_value')
            + $netCost;

        $discount = $this->proposta_desconto > 0
            ? round($subtotal * (min(100, max(0, $this->proposta_desconto)) / 100), 2)
            : 0;

        $proposal = \App\Models\Proposal::create([
            'number'      => 'PROP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
            'partner_id'  => $partner->id,
            'customer_id' => $this->customer->id,
            'created_by'  => auth()->id(),
            'title'       => $this->proposta_titulo ?: 'Proposta Cloud',
            'status'      => 'draft',
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'total'       => $subtotal - $discount,
            'valid_until' => $this->proposta_validade ?: null,
            'notes'       => $this->proposta_notas ?: null,
        ]);

        $sort = 1;
        if ($rede) {
            \App\Models\ProposalItem::create([
                'proposal_id' => $proposal->id,
                'name'        => 'Rede: ' . ($rede->networkType?->name ?? 'Rede'),
                'description' => 'Conexão de rede compartilhada entre todas as VMs',
                'quantity'    => 1,
                'unit_price'  => $netCost,
                'total'       => $netCost,
                'sort_order'  => $sort++,
            ]);
        }
        foreach ($vms as $vm) {
            $desc = collect([
                $vm->osDistribution?->name,
                $vm->cpu_cores . ' vCPUs',
                $vm->ram_gb . 'GB RAM',
                ($vm->disk_os_gb + $vm->additionalDisks->sum('size_gb')) . 'GB Armazenamento',
                $vm->price_os_license > 0 ? 'Lic. Windows' : null,
                $vm->price_rds > 0        ? 'Terminal ' . $vm->rds_license_qty . 'x' : null,
                $vm->endpointSecurity     ? 'Endpoint Security' : null,
                $vm->has_backup           ? 'Backup incluso' : null,
            ])->filter()->implode(' | ');
            \App\Models\ProposalItem::create([
                'proposal_id' => $proposal->id,
                'name'        => 'VM: ' . $vm->name,
                'description' => $desc,
                'quantity'    => 1,
                'unit_price'  => $vm->price_total_monthly,
                'total'       => $vm->price_total_monthly,
                'sort_order'  => $sort++,
            ]);
        }
        foreach ($s3s as $s3) {
            $s3Total = $s3->size_gb * $s3->price_per_gb;
            \App\Models\ProposalItem::create([
                'proposal_id' => $proposal->id,
                'name'        => 'Object Storage S3',
                'description' => $s3->size_gb . ' GB · R$ ' . number_format($s3->price_per_gb, 4, ',', '.') . '/GB',
                'quantity'    => 1,
                'unit_price'  => $s3Total,
                'total'       => $s3Total,
                'sort_order'  => $sort++,
            ]);
        }
        foreach ($backups as $bkp) {
            \App\Models\ProposalItem::create([
                'proposal_id' => $proposal->id,
                'name'        => 'Backup Gerenciado',
                'description' => ($bkp->machine_count ?? 1) . ' máquina(s) · ' . ($bkp->total_disk_gb ?? 0) . 'GB',
                'quantity'    => 1,
                'unit_price'  => $bkp->monthly_value,
                'total'       => $bkp->monthly_value,
                'sort_order'  => $sort++,
            ]);
        }
        $this->modalProposta = false;
        $this->redirect(route('proposta.imprimir', $proposal->id));
    }

    // ─── Helpers Privados ─────────────────────────────────────────
    private function resetFormVm(): void
    {
        $this->form_os_family_id = $this->form_os_distribution_id = $this->form_disk_os_type_id = null;
        $this->form_remote_desktop_type_id = $this->form_rds_license_mode_id = $this->form_endpoint_security_id = null;
        $this->form_backup_retention_id = $this->form_backup_software_id = null;
        $this->form_vm_name = $this->form_vm_description = '';
        $this->form_cpu_cores = 2; $this->form_ram_gb = 4; $this->form_disk_os_gb = 80; $this->form_rds_license_qty = 5;
        $this->form_has_additional_disks = $this->form_has_remote_desktop = $this->form_has_endpoint = $this->form_has_backup_vm = false;
        $this->form_additional_disks = [];
    }

    private function resetFormRede(): void
    {
        $this->form_network_type_id     = \App\Models\NetworkType::where('slug', 'vpc')->value('id');
        $this->form_bandwidth_option_id = \App\Models\BandwidthOption::orderBy('sort_order')->value('id');
        $this->form_extra_public_ips    = 0;
    }

    private function getUnitPrice(string $type): float
    {
        return (float) (\App\Models\ResourcePricing::where('resource_type', $type)->value('price') ?? 0);
    }

    private function calcPricesVm(): array
    {
        $cpuCores   = (int) $this->form_cpu_cores;
        $ramGb      = (int) $this->form_ram_gb;
        $diskOsGb   = (int) $this->form_disk_os_gb;
        $diskTypeId = (int) ($this->form_disk_os_type_id ?? 0);

        $priceCpu    = $cpuCores * $this->getUnitPrice('cpu_core');
        $priceRam    = $ramGb * $this->getUnitPrice('ram_gb');
        $diskType    = $diskTypeId ? \App\Models\DiskType::find($diskTypeId) : null;
        $priceDiskOs = $diskOsGb * (float) ($diskType?->price_per_gb ?? 0);
        $os          = $this->form_os_distribution_id ? \App\Models\OsDistribution::find((int) $this->form_os_distribution_id) : null;
        $priceOsLicense = $os ? $os->calculateLicenseCost($cpuCores) : 0;

        $priceRds = 0;
        if ($this->form_has_remote_desktop && $this->form_rds_license_mode_id && $this->form_rds_license_qty > 0) {
            $rdsMode  = \App\Models\RdsLicenseMode::find($this->form_rds_license_mode_id);
            $priceRds = (float) ($rdsMode?->price_per_unit ?? 0) * $this->form_rds_license_qty;
        }
        $priceEndpoint = 0;
        if ($this->form_has_endpoint && $this->form_endpoint_security_id) {
            $ep            = \App\Models\EndpointSecurityOption::find($this->form_endpoint_security_id);
            $priceEndpoint = (float) ($ep?->price_per_vm ?? 0);
        }
        $priceBackupSw = $priceBackup = $backupStorageGb = 0;
        if ($this->form_has_backup_vm) {
            $bkpSw         = $this->form_backup_software_id ? \App\Models\BackupSoftwareOption::find($this->form_backup_software_id) : null;
            $priceBackupSw = $bkpSw ? $bkpSw->calculateCost(1) : 0;
            $totalDiskGb   = $diskOsGb + array_sum(array_column($this->form_additional_disks, 'size_gb'));
            $backupStorageGb = $totalDiskGb * 0.5;
            $retention     = $this->form_backup_retention_id ? \App\Models\BackupRetentionOption::find($this->form_backup_retention_id) : null;
            $priceBackup   = $backupStorageGb * $this->getUnitPrice('backup_gb') * ($retention ? (float) $retention->price_multiplier : 1);
        }
        $priceAdditionalDisks = 0;
        foreach ($this->form_additional_disks as $d) {
            $addType              = isset($d['disk_type_id']) ? \App\Models\DiskType::find((int) $d['disk_type_id']) : null;
            $priceAdditionalDisks += (int) ($d['size_gb'] ?? 0) * (float) ($addType?->price_per_gb ?? 0);
        }
        $total = $priceCpu + $priceRam + $priceDiskOs + $priceOsLicense + $priceRds
               + $priceEndpoint + $priceBackupSw + $priceBackup + $priceAdditionalDisks;

        return compact('priceCpu', 'priceRam', 'priceDiskOs', 'priceOsLicense', 'priceRds',
                       'priceEndpoint', 'priceBackupSw', 'priceBackup', 'backupStorageGb', 'total');
    }

    private function persistirVm(?int $vmId, ?int $projectId): void
    {
        if (!$projectId) {
            \Filament\Notifications\Notification::make()->title('Projeto não encontrado')->danger()->send();
            return;
        }
        $prices = $this->calcPricesVm();
        $vmData = [
            'project_id'             => $projectId,
            'name'                   => $this->form_vm_name,
            'description'            => $this->form_vm_description ?: null,
            'cpu_cores'              => (int) $this->form_cpu_cores,
            'ram_gb'                 => (int) $this->form_ram_gb,
            'disk_os_gb'             => (int) $this->form_disk_os_gb,
            'disk_os_type_id'        => (int) $this->form_disk_os_type_id,
            'os_distribution_id'     => (int) $this->form_os_distribution_id,
            'remote_desktop_type_id' => $this->form_has_remote_desktop ? $this->form_remote_desktop_type_id : null,
            'rds_license_mode_id'    => $this->form_has_remote_desktop ? $this->form_rds_license_mode_id : null,
            'rds_license_qty'        => $this->form_has_remote_desktop ? (int) $this->form_rds_license_qty : 0,
            'endpoint_security_id'   => $this->form_has_endpoint ? $this->form_endpoint_security_id : null,
            'has_backup'             => $this->form_has_backup_vm,
            'backup_retention_id'    => $this->form_has_backup_vm ? $this->form_backup_retention_id : null,
            'backup_software_id'     => $this->form_has_backup_vm ? $this->form_backup_software_id : null,
            'backup_storage_gb'      => $prices['backupStorageGb'],
            'price_cpu'              => $prices['priceCpu'],
            'price_ram'              => $prices['priceRam'],
            'price_disk_os'          => $prices['priceDiskOs'],
            'price_os_license'       => $prices['priceOsLicense'],
            'price_rds'              => $prices['priceRds'],
            'price_endpoint'         => $prices['priceEndpoint'],
            'price_backup'           => $prices['priceBackup'],
            'price_backup_software'  => $prices['priceBackupSw'],
            'price_total_monthly'    => $prices['total'],
            'status'                 => 'active',
        ];
        if ($vmId) {
            $vm = \App\Models\ProjectVm::findOrFail($vmId);
            $vm->update($vmData);
            $vm->additionalDisks()->delete();
        } else {
            $vm = \App\Models\ProjectVm::create($vmData);
        }
        if ($this->form_has_additional_disks) {
            foreach ($this->form_additional_disks as $idx => $disk) {
                $addType = isset($disk['disk_type_id']) ? \App\Models\DiskType::find((int) $disk['disk_type_id']) : null;
                \App\Models\ProjectVmDisk::create([
                    'project_vm_id' => $vm->id,
                    'disk_type_id'  => (int) $disk['disk_type_id'],
                    'size_gb'       => (int) $disk['size_gb'],
                    'sort_order'    => $idx,
                    'price'         => (int) ($disk['size_gb'] ?? 0) * (float) ($addType?->price_per_gb ?? 0),
                ]);
            }
        }
        $this->recalcProject($projectId);
        $this->fecharModais();
        \Filament\Notifications\Notification::make()
            ->title($vmId ? 'VM atualizada' : 'VM cadastrada')
            ->body("VM: {$vm->name} — R$ " . number_format($prices['total'], 2, ',', '.') . '/mês')
            ->success()->send();
        $this->redirect(static::getUrl(['record' => $this->customer]));
    }

    private function recalcProject(int $projectId): void
    {
        $project = \App\Models\Project::with(['vms', 'bandwidthOption'])->find($projectId);
        if (!$project) return;
        $project->monthly_value = $project->vms()->where('status', 'active')->sum('price_total_monthly')
            + ($project->extra_ip_price ?? 0)
            + (float) ($project->bandwidthOption?->price ?? 0);
        $project->save();
    }
}
