<?php

namespace App\Services;

use App\Models\Project;

class ProjectPricingService
{
    /**
     * Retorna todos os itens do projeto com preços detalhados
     */
    public function getDetailedPricing(Project $project): array
    {
        $items = [];
        
        // Rede
        $networkCost = $project->getNetworkCost();
        $networkDiscount = $project->network_discount_amount ?? 0;
        
        if ($networkCost > 0) {
            $items[] = [
                'type' => 'network',
                'id' => 'network',
                'name' => 'Rede do Cliente',
                'description' => $this->getNetworkDescription($project),
                'subtotal' => $networkCost,
                'discount' => $networkDiscount,
                'total' => max(0, $networkCost - $networkDiscount),
            ];
        }

        // VMs
        foreach ($project->vms as $vm) {
            $vmCost = $vm->getTotalMonthlyCost();
            
            $items[] = [
                'type' => 'vm',
                'id' => $vm->id,
                'name' => $vm->name,
                'description' => $this->getVmDescription($vm),
                'subtotal' => $vmCost,
                'discount' => $vm->discount_amount ?? 0,
                'total' => $vm->getTotalMonthlyCostWithDiscount(),
                'details' => [
                    'cpu_cores' => $vm->cpu_cores,
                    'ram_gb' => $vm->ram_gb,
                    'os' => $vm->osDistribution?->name,
                    'disks' => $vm->disks->count(),
                ],
            ];
        }

        // S3 Storage
        foreach ($project->s3Storage as $s3) {
            $s3Cost = $s3->getTotalMonthlyCost();
            
            $items[] = [
                'type' => 's3',
                'id' => $s3->id,
                'name' => $s3->name ?? ('S3 Storage - ' . $s3->quantity_gb . 'GB'),
                'description' => 'Armazenamento S3 - ' . $s3->quantity_gb . 'GB',
                'subtotal' => $s3Cost,
                'discount' => $s3->discount_amount ?? 0,
                'total' => $s3->getTotalMonthlyCostWithDiscount(),
            ];
        }

        // Backup Standalone
        foreach ($project->backupStandalone as $backup) {
            $backupCost = $backup->getTotalMonthlyCost();
            
            $items[] = [
                'type' => 'backup',
                'id' => $backup->id,
                'name' => $backup->name ?? ('Backup Standalone - ' . $backup->total_storage_gb . 'GB'),
                'description' => $this->getBackupDescription($backup),
                'subtotal' => $backupCost,
                'discount' => $backup->discount_amount ?? 0,
                'total' => $backup->getTotalMonthlyCostWithDiscount(),
            ];
        }

        $subtotalGeneral = array_sum(array_column($items, 'subtotal'));
        $discountGeneral = array_sum(array_column($items, 'discount'));
        $totalBeforeGlobalDiscount = array_sum(array_column($items, 'total'));
        
        return [
            'items' => $items,
            'summary' => [
                'subtotal' => round($subtotalGeneral, 2),
                'item_discounts' => round($discountGeneral, 2),
                'total_before_global_discount' => round($totalBeforeGlobalDiscount, 2),
                'global_discount' => $project->global_discount_amount ?? 0,
                'total' => $project->getTotalMonthlyCost(),
                'currency' => $project->currency ?? 'BRL',
                'partner_commission' => $project->partner_commission_percentage ?? 0,
            ],
        ];
    }

    private function getNetworkDescription($project): string
    {
        $parts = [];
        
        if ($project->networkType) {
            $parts[] = $project->networkType->name;
        }
        
        if ($project->bandwidthOption) {
            $parts[] = $project->bandwidthOption->name;
        }
        
        if ($project->has_firewall && $project->firewallOption) {
            $parts[] = 'Firewall: ' . $project->firewallOption->name;
        }
        
        return !empty($parts) ? implode(' | ', $parts) : 'Rede padrão';
    }

    private function getVmDescription($vm): string
    {
        $parts = [
            $vm->cpu_cores . ' vCPUs',
            $vm->ram_gb . 'GB RAM',
        ];
        
        if ($vm->osDistribution) {
            $parts[] = $vm->osDistribution->name;
        }
        
        return implode(' | ', $parts);
    }

    private function getBackupDescription($backup): string
    {
        $parts = [$backup->total_storage_gb . 'GB'];
        
        if ($backup->backupSoftware) {
            $parts[] = $backup->backupSoftware->name;
        }
        
        if ($backup->backupRetention) {
            $parts[] = $backup->backupRetention->name;
        }
        
        return implode(' | ', $parts);
    }
}
