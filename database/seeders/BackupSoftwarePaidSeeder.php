<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupSoftwarePaidSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('backup_software_options')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('backup_software_options')->insert([
            [
                'name'            => 'Veeam Backup & Replication',
                'slug'            => 'veeam-vbr',
                'edition'         => 'Universal License',
                'license_model'   => 'per_vm',
                'billing_cycle'   => 'monthly',
                'price_per_unit'  => 0.00, // admin define o preço
                'included_units'  => 0,
                'has_agent'       => false,
                'price_per_agent' => 0.00,
                'is_active'       => true,
                'sort_order'      => 1,
                'notes'           => 'Licença mensal por VM. Preço definido pelo admin.',
                'created_at'      => now(), 'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Veeam Backup & Replication inserido!');
    }
}
