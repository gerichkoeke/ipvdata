<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfrastructureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tipos de Rede
        DB::table('network_types')->insertOrIgnore([
            ['name' => 'VPC',         'slug' => 'vpc',         'has_public_ip' => true,  'default_ips' => 1, 'requires_firewall' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Firewall',    'slug' => 'firewall',    'has_public_ip' => true,  'default_ips' => 1, 'requires_firewall' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LAN-to-LAN', 'slug' => 'lan-to-lan',  'has_public_ip' => false, 'default_ips' => 0, 'requires_firewall' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Opções de Firewall
        DB::table('firewall_options')->insertOrIgnore([
            ['name' => 'Fortinet',  'slug' => 'fortinet',  'price' => 0.00, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sophos',    'slug' => 'sophos',    'price' => 0.00, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pfSense',   'slug' => 'pfsense',   'price' => 0.00, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OPNsense',  'slug' => 'opnsense',  'price' => 0.00, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Famílias de OS
        $linuxId   = DB::table('os_families')->insertGetId(['name' => 'Linux',   'slug' => 'linux',   'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $windowsId = DB::table('os_families')->insertGetId(['name' => 'Windows', 'slug' => 'windows', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()]);

        // Verificar se já existem (caso rode o seeder duas vezes)
        if (!$linuxId) {
            $linuxId   = DB::table('os_families')->where('slug', 'linux')->value('id');
            $windowsId = DB::table('os_families')->where('slug', 'windows')->value('id');
        }

        // 4. Distribuições Linux
        DB::table('os_distributions')->insertOrIgnore([
            ['os_family_id' => $linuxId, 'name' => 'Ubuntu 22.04 LTS',    'version' => '22.04', 'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'Ubuntu 24.04 LTS',    'version' => '24.04', 'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'Debian 11',           'version' => '11',    'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'Debian 12',           'version' => '12',    'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'CentOS Stream 9',     'version' => '9',     'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'Rocky Linux 9',       'version' => '9',     'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'AlmaLinux 9',         'version' => '9',     'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 7,  'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $linuxId, 'name' => 'Oracle Linux 9',      'version' => '9',     'price' => 0, 'requires_license' => false, 'is_active' => true, 'sort_order' => 8,  'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. Distribuições Windows
        DB::table('os_distributions')->insertOrIgnore([
            ['os_family_id' => $windowsId, 'name' => 'Windows Server 2019 Standard',   'version' => '2019', 'price' => 0, 'requires_license' => true, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $windowsId, 'name' => 'Windows Server 2022 Standard',   'version' => '2022', 'price' => 0, 'requires_license' => true, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $windowsId, 'name' => 'Windows Server 2019 Datacenter', 'version' => '2019', 'price' => 0, 'requires_license' => true, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['os_family_id' => $windowsId, 'name' => 'Windows Server 2022 Datacenter', 'version' => '2022', 'price' => 0, 'requires_license' => true, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Remote Desktop Types
        $rdsId    = DB::table('remote_desktop_types')->insertGetId(['name' => 'RDS Microsoft', 'slug' => 'rds',    'has_license_modes' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $tsplusId = DB::table('remote_desktop_types')->insertGetId(['name' => 'TSPLUS',        'slug' => 'tsplus', 'has_license_modes' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()]);

        if (!$rdsId) {
            $rdsId    = DB::table('remote_desktop_types')->where('slug', 'rds')->value('id');
            $tsplusId = DB::table('remote_desktop_types')->where('slug', 'tsplus')->value('id');
        }

        // 7. Modos de Licença RDS
        DB::table('rds_license_modes')->insertOrIgnore([
            ['remote_desktop_type_id' => $rdsId, 'name' => 'Por Device',  'slug' => 'per-device', 'price_per_unit' => 0.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['remote_desktop_type_id' => $rdsId, 'name' => 'Por Usuário', 'slug' => 'per-user',   'price_per_unit' => 0.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            // TSPLUS também tem modos
            ['remote_desktop_type_id' => $tsplusId, 'name' => 'Por Usuário Simultâneo', 'slug' => 'tsplus-concurrent', 'price_per_unit' => 0.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 8. Tipos de Disco
        DB::table('disk_types')->insertOrIgnore([
            ['name' => 'NVMe', 'slug' => 'nvme', 'price_per_gb' => 0.0000, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SSD',  'slug' => 'ssd',  'price_per_gb' => 0.0000, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HDD',  'slug' => 'hdd',  'price_per_gb' => 0.0000, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 9. Endpoint Security
        DB::table('endpoint_security_options')->insertOrIgnore([
            ['name' => 'SentinelOne', 'slug' => 'sentinelone', 'price_per_vm' => 0.00, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 10. Retenção de Backup
        DB::table('backup_retention_options')->insertOrIgnore([
            ['name' => '3 dias',   'days' => 3,    'is_full' => false, 'price_multiplier' => 0.25, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '7 dias',   'days' => 7,    'is_full' => false, 'price_multiplier' => 0.50, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '15 dias',  'days' => 15,   'is_full' => false, 'price_multiplier' => 0.75, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '30 dias',  'days' => 30,   'is_full' => false, 'price_multiplier' => 1.00, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Full',     'days' => null, 'is_full' => true,  'price_multiplier' => 1.50, 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 11. Opções de Banda
        DB::table('bandwidth_options')->insertOrIgnore([
            ['name' => '10 Mbps',   'mbps' => 10,   'price' => 0.00, 'is_active' => true, 'sort_order' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '20 Mbps',   'mbps' => 20,   'price' => 0.00, 'is_active' => true, 'sort_order' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '50 Mbps',   'mbps' => 50,   'price' => 0.00, 'is_active' => true, 'sort_order' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '100 Mbps',  'mbps' => 100,  'price' => 0.00, 'is_active' => true, 'sort_order' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '200 Mbps',  'mbps' => 200,  'price' => 0.00, 'is_active' => true, 'sort_order' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '500 Mbps',  'mbps' => 500,  'price' => 0.00, 'is_active' => true, 'sort_order' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '1000 Mbps', 'mbps' => 1000, 'price' => 0.00, 'is_active' => true, 'sort_order' => 7,  'created_at' => now(), 'updated_at' => now()],
        ]);

        // 12. Tabela de Preços de Recursos (todos zerados — admin define os preços)
        DB::table('resource_pricing')->insertOrIgnore([
            ['resource_type' => 'cpu_core',   'name' => 'vCPU por Core',          'price' => 0.0000, 'unit' => 'vCore', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['resource_type' => 'ram_gb',     'name' => 'Memória RAM por GB',      'price' => 0.0000, 'unit' => 'GB',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['resource_type' => 'public_ip',  'name' => 'IP Público Adicional',    'price' => 0.0000, 'unit' => 'IP',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['resource_type' => 's3_gb',      'name' => 'Armazenamento S3 por GB', 'price' => 0.0000, 'unit' => 'GB',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['resource_type' => 'backup_gb',  'name' => 'Backup S3 por GB',        'price' => 0.0000, 'unit' => 'GB',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✅ Infrastructure seeder concluído!');
    }
}
