<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MsLicenseSkuFullSeeder extends Seeder
{
    public function run(): void
    {
        $distros = DB::table('os_distributions')
            ->whereIn('name', [
                'Windows Server 2019 Standard',
                'Windows Server 2019 Datacenter',
                'Windows Server 2022 Standard',
                'Windows Server 2022 Datacenter',
                'Windows Server 2025 Standard',
                'Windows Server 2025 Datacenter',
            ])
            ->pluck('id', 'name');

        $rdTypes = DB::table('remote_desktop_types')->pluck('id', 'slug');

        $skus = [];

        // ── Windows Server 2019 ──────────────────────────────────────
        foreach ([
            ['Windows Server 2019 Standard',   'standard',   '9EM-00593'],
            ['Windows Server 2019 Datacenter',  'datacenter', '9EM-00633'],
        ] as [$distro, $type, $pn]) {
            $id = $distros[$distro] ?? null;
            if (!$id) continue;
            foreach ([
                ['2-Core Pack', 2, '1year'],
                ['2-Core Pack', 2, '3year'],
                ['8-Core Pack', 8, '1year'],
                ['8-Core Pack', 8, '3year'],
            ] as [$packName, $packSize, $period]) {
                $skus[] = [
                    'os_distribution_id' => $id,
                    'name'               => "Windows Server 2019 {$type} - {$packName} {$period}",
                    'part_number'        => $pn,
                    'license_type'       => $type,
                    'cores_per_license'  => 16,
                    'pack_size'          => $packSize,
                    'threshold_cores'    => 16,
                    'billing_period'     => $period,
                    'is_cal'             => false,
                    'sa_available'       => true,
                    'cost_price'         => 0,
                    'sale_price'         => 0,
                    'is_active'          => true,
                ];
            }
        }

        // ── Windows Server 2022 ──────────────────────────────────────
        foreach ([
            ['Windows Server 2022 Standard',   'standard',   '9EM-00653'],
            ['Windows Server 2022 Datacenter',  'datacenter', '9EM-00695'],
        ] as [$distro, $type, $pn]) {
            $id = $distros[$distro] ?? null;
            if (!$id) continue;
            foreach ([
                ['2-Core Pack', 2, '1year'],
                ['2-Core Pack', 2, '3year'],
                ['8-Core Pack', 8, '1year'],
                ['8-Core Pack', 8, '3year'],
            ] as [$packName, $packSize, $period]) {
                $skus[] = [
                    'os_distribution_id' => $id,
                    'name'               => "Windows Server 2022 {$type} - {$packName} {$period}",
                    'part_number'        => $pn,
                    'license_type'       => $type,
                    'cores_per_license'  => 16,
                    'pack_size'          => $packSize,
                    'threshold_cores'    => 16,
                    'billing_period'     => $period,
                    'is_cal'             => false,
                    'sa_available'       => true,
                    'cost_price'         => 0,
                    'sale_price'         => 0,
                    'is_active'          => true,
                ];
            }
        }

        // ── Windows Server 2025 ──────────────────────────────────────
        foreach ([
            ['Windows Server 2025 Standard',   'standard',   'WS25STD'],
            ['Windows Server 2025 Datacenter',  'datacenter', 'WS25DC'],
        ] as [$distro, $type, $pn]) {
            $id = $distros[$distro] ?? null;
            if (!$id) continue;
            foreach ([
                ['2-Core Pack', 2, '1year'],
                ['2-Core Pack', 2, '3year'],
                ['8-Core Pack', 8, '1year'],
                ['8-Core Pack', 8, '3year'],
                ['2-Core Pack', 2, 'monthly'],
                ['8-Core Pack', 8, 'monthly'],
            ] as [$packName, $packSize, $period]) {
                $skus[] = [
                    'os_distribution_id' => $id,
                    'name'               => "Windows Server 2025 {$type} - {$packName} " . strtoupper($period),
                    'part_number'        => $pn,
                    'license_type'       => $type,
                    'cores_per_license'  => 16,
                    'pack_size'          => $packSize,
                    'threshold_cores'    => 16,
                    'billing_period'     => $period,
                    'is_cal'             => false,
                    'sa_available'       => true,
                    'cost_price'         => 0,
                    'sale_price'         => 0,
                    'is_active'          => true,
                ];
            }
        }

        // ── CALs RDS (Remote Desktop Services) ──────────────────────
        // Windows Server 2025 CAL
        $ws2025stdId = $distros['Windows Server 2025 Standard'] ?? null;
        if ($ws2025stdId) {
            foreach ([
                ['User CAL',   'user',   '1year'],
                ['User CAL',   'user',   '3year'],
                ['Device CAL', 'device', '1year'],
                ['Device CAL', 'device', '3year'],
                ['User CAL',   'user',   'monthly'],
                ['Device CAL', 'device', 'monthly'],
            ] as [$calName, $calType, $period]) {
                $skus[] = [
                    'os_distribution_id' => $ws2025stdId,
                    'name'               => "Windows Server 2025 CAL - 1 {$calName} " . strtoupper($period),
                    'part_number'        => $calType === 'user' ? 'R18-06367' : 'R18-06368',
                    'license_type'       => 'standard',
                    'cores_per_license'  => 1,
                    'pack_size'          => 1,
                    'threshold_cores'    => 0,
                    'billing_period'     => $period,
                    'is_cal'             => true,
                    'cal_type'           => $calType,
                    'sa_available'       => true,
                    'cost_price'         => 0,
                    'sale_price'         => 0,
                    'is_active'          => true,
                ];
            }
        }

        // ── Remote Desktop Services (RDS) ────────────────────────────
        if ($ws2025stdId) {
            foreach ([
                ['User CAL',   'user',   '1year'],
                ['User CAL',   'user',   '3year'],
                ['Device CAL', 'device', '1year'],
                ['Device CAL', 'device', '3year'],
            ] as [$calName, $calType, $period]) {
                $skus[] = [
                    'os_distribution_id' => $ws2025stdId,
                    'name'               => "Windows Server 2025 Remote Desktop Services - 1 {$calName} " . strtoupper($period),
                    'part_number'        => 'RDS-2025-' . strtoupper($calType),
                    'license_type'       => 'standard',
                    'cores_per_license'  => 1,
                    'pack_size'          => 1,
                    'threshold_cores'    => 0,
                    'billing_period'     => $period,
                    'is_cal'             => true,
                    'cal_type'           => $calType,
                    'sa_available'       => true,
                    'cost_price'         => 0,
                    'sale_price'         => 0,
                    'is_active'          => true,
                ];
            }
        }

        // Limpar SKUs antigos e inserir os novos (desabilitar FK temporariamente)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('ms_license_skus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($skus as $sku) {
            DB::table('ms_license_skus')->insert(
                array_merge($sku, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('✅ ' . count($skus) . ' SKUs Microsoft inseridos!');
    }
}
