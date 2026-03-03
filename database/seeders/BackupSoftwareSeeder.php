<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupSoftwareSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('backup_software_options')->insertOrIgnore([
            // Veeam Backup & Replication
            [
                'name'           => 'Veeam Backup & Replication Community',
                'slug'           => 'veeam-community',
                'edition'        => 'Community',
                'license_model'  => 'per_vm',
                'price_per_unit' => 0.00,
                'included_units' => 10,
                'has_agent'      => false,
                'price_per_agent'=> 0.00,
                'is_active'      => true,
                'sort_order'     => 1,
                'notes'          => 'Gratuito, limitado a 10 VMs',
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Veeam Backup Essentials',
                'slug'           => 'veeam-essentials',
                'edition'        => 'Essentials',
                'license_model'  => 'per_vm',
                'price_per_unit' => 0.00,
                'included_units' => 0,
                'has_agent'      => true,
                'price_per_agent'=> 0.00,
                'is_active'      => true,
                'sort_order'     => 2,
                'notes'          => 'Até 6 sockets',
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Veeam Backup & Replication Universal',
                'slug'           => 'veeam-universal',
                'edition'        => 'Universal',
                'license_model'  => 'per_vm',
                'price_per_unit' => 0.00,
                'included_units' => 0,
                'has_agent'      => true,
                'price_per_agent'=> 0.00,
                'is_active'      => true,
                'sort_order'     => 3,
                'notes'          => 'Licença por instância (VM ou agente)',
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Veeam Backup & Replication Enterprise Plus',
                'slug'           => 'veeam-enterprise-plus',
                'edition'        => 'Enterprise Plus',
                'license_model'  => 'per_vm',
                'price_per_unit' => 0.00,
                'included_units' => 0,
                'has_agent'      => true,
                'price_per_agent'=> 0.00,
                'is_active'      => true,
                'sort_order'     => 4,
                'notes'          => 'Recursos avançados: CDP, Instant Recovery, etc',
                'created_at'     => now(), 'updated_at' => now(),
            ],
            // Veeam Agent (para máquinas f��sicas / backup standalone)
            [
                'name'           => 'Veeam Agent for Linux',
                'slug'           => 'veeam-agent-linux',
                'edition'        => 'Agent',
                'license_model'  => 'per_vm',
                'price_per_unit' => 0.00,
                'included_units' => 0,
                'has_agent'      => true,
                'price_per_agent'=> 0.00,
                'is_active'      => true,
                'sort_order'     => 5,
                'notes'          => 'Para servidores Linux físicos ou VMs fora do ambiente',
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Veeam Agent for Windows',
                'slug'           => 'veeam-agent-windows',
                'edition'        => 'Agent',
                'license_model'  => 'per_vm',
                'price_per_unit' => 0.00,
                'included_units' => 0,
                'has_agent'      => true,
                'price_per_agent'=> 0.00,
                'is_active'      => true,
                'sort_order'     => 6,
                'notes'          => 'Para servidores Windows físicos ou VMs fora do ambiente',
                'created_at'     => now(), 'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ ' . DB::table('backup_software_options')->count() . ' opções de backup software inseridas!');
    }
}
