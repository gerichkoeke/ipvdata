<?php

namespace Database\Seeders;

use App\Models\ResourcePricing;
use Illuminate\Database\Seeder;

class ResourcePricingSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['resource_type' => 'cpu_core',  'name' => 'vCPU por Core',          'unit' => 'vCore'],
            ['resource_type' => 'ram_gb',    'name' => 'Memória RAM por GB',      'unit' => 'GB'],
            ['resource_type' => 'public_ip', 'name' => 'IP Público Adicional',    'unit' => 'IP'],
            ['resource_type' => 's3_gb',     'name' => 'Armazenamento S3 por GB', 'unit' => 'GB'],
            ['resource_type' => 'backup_gb', 'name' => 'Backup S3 por GB',        'unit' => 'GB'],
        ];

        foreach ($records as $record) {
            ResourcePricing::updateOrCreate(
                ['resource_type' => $record['resource_type']],
                ['name' => $record['name'], 'price' => 0.00, 'unit' => $record['unit'], 'is_active' => true]
            );
        }

        $this->command->info('✅ ResourcePricingSeeder concluído!');
    }
}
