<?php
namespace App\Services;

use App\Models\BackupRetentionOption;

class BackupStorageCalculator
{
    /**
     * Calcula o armazenamento S3 necessário em GB
     *
     * @param float $totalDiskGb  Total de discos da VM (OS + adicionais)
     * @param BackupRetentionOption $retention
     * @return array ['storage_gb', 'full_gb', 'incremental_gb', 'fulls_count', 'incrementals_count', 'description']
     */
    public static function calculate(float $totalDiskGb, BackupRetentionOption $retention): array
    {
        $days        = $retention->days ?? 30;
        $compression = 1 - ($retention->compression_rate / 100); // 40% compressão → fator 0.60
        $changeRate  = $retention->change_rate / 100;            // 10% → 0.10

        $fullSizeGb  = $totalDiskGb * $compression;  // tamanho de 1 full comprimido
        $incrSizeGb  = $totalDiskGb * $changeRate * $compression; // tamanho de 1 incremental

        switch ($retention->full_frequency) {

            case 'daily':
                // Full todo dia, sem incrementais
                $fullsCount  = $days;
                $incrsCount  = 0;
                $totalGb     = $fullSizeGb * $fullsCount;
                $description = "{$fullsCount} fulls diários × " . round($fullSizeGb, 2) . "GB";
                break;

            case 'weekly':
                // 1 full por semana + incrementais nos demais dias
                $fullsCount  = (int) ceil($days / 7);
                $incrsCount  = $days - $fullsCount;
                $totalGb     = ($fullSizeGb * $fullsCount) + ($incrSizeGb * $incrsCount);
                $description = "{$fullsCount} fulls semanais + {$incrsCount} incrementais";
                break;

            case 'monthly':
                // 1 full por mês + incrementais nos demais dias
                $fullsCount  = (int) ceil($days / 30);
                $incrsCount  = $days - $fullsCount;
                $totalGb     = ($fullSizeGb * $fullsCount) + ($incrSizeGb * $incrsCount);
                $description = "{$fullsCount} full mensal + {$incrsCount} incrementais";
                break;

            case 'incremental':
            default:
                // 1 full inicial + incrementais diários
                $fullsCount  = 1;
                $incrsCount  = $days - 1;
                $totalGb     = ($fullSizeGb * $fullsCount) + ($incrSizeGb * $incrsCount);
                $description = "1 full + {$incrsCount} incrementais diários";
                break;
        }

        return [
            'storage_gb'         => round($totalGb, 2),
            'full_gb'            => round($fullSizeGb, 2),
            'incremental_gb'     => round($incrSizeGb, 2),
            'fulls_count'        => $fullsCount,
            'incrementals_count' => $incrsCount,
            'compression_factor' => $compression,
            'change_rate'        => $changeRate,
            'description'        => $description,
        ];
    }

    /**
     * Calcula para backup standalone (sem VM, múltiplas máquinas)
     */
    public static function calculateStandalone(
        int $vmCount,
        float $storagePerVmGb,
        BackupRetentionOption $retention
    ): array {
        $totalDiskGb = $vmCount * $storagePerVmGb;
        $result      = self::calculate($totalDiskGb, $retention);
        $result['vm_count']          = $vmCount;
        $result['storage_per_vm_gb'] = $storagePerVmGb;
        $result['total_disk_gb']     = $totalDiskGb;
        return $result;
    }
}
