<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MsLicenseSkuSeeder extends Seeder
{
    public function run(): void
    {
        // Pegar IDs das distros Windows
        $distros = DB::table('os_distributions')
            ->whereIn('name', [
                'Windows Server 2019 Standard',
                'Windows Server 2022 Standard',
                'Windows Server 2019 Datacenter',
                'Windows Server 2022 Datacenter',
            ])
            ->pluck('id', 'name');

        $skus = [
            [
                'os_distribution_id' => $distros['Windows Server 2022 Standard']   ?? null,
                'name'               => 'Windows Server 2022 Standard - 16 Core',
                'part_number'        => '9EM-00653',
                'license_type'       => 'standard',
                'cores_per_license'  => 16,
                'threshold_cores'    => 16,
                'sa_available'       => true,
                'cost_price'         => 0,
                'sale_price'         => 0,
                'is_active'          => true,
            ],
            [
                'os_distribution_id' => $distros['Windows Server 2022 Datacenter'] ?? null,
                'name'               => 'Windows Server 2022 Datacenter - 16 Core',
                'part_number'        => '9EM-00695',
                'license_type'       => 'datacenter',
                'cores_per_license'  => 16,
                'threshold_cores'    => 16,
                'sa_available'       => true,
                'cost_price'         => 0,
                'sale_price'         => 0,
                'is_active'          => true,
            ],
            [
                'os_distribution_id' => $distros['Windows Server 2019 Standard']   ?? null,
                'name'               => 'Windows Server 2019 Standard - 16 Core',
                'part_number'        => '9EM-00593',
                'license_type'       => 'standard',
                'cores_per_license'  => 16,
                'threshold_cores'    => 16,
                'sa_available'       => true,
                'cost_price'         => 0,
                'sale_price'         => 0,
                'is_active'          => true,
            ],
            [
                'os_distribution_id' => $distros['Windows Server 2019 Datacenter'] ?? null,
                'name'               => 'Windows Server 2019 Datacenter - 16 Core',
                'part_number'        => '9EM-00633',
                'license_type'       => 'datacenter',
                'cores_per_license'  => 16,
                'threshold_cores'    => 16,
                'sa_available'       => true,
                'cost_price'         => 0,
                'sale_price'         => 0,
                'is_active'          => true,
            ],
        ];

        foreach ($skus as $sku) {
            if ($sku['os_distribution_id']) {
                DB::table('ms_license_skus')->insertOrIgnore(
                    array_merge($sku, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        $this->command->info('✅ MS License SKUs criados!');
    }
}
