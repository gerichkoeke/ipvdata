<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupRetentionFullSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('backup_retention_options')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('backup_retention_options')->insert([
            // ── Retenção Incremental ─────────────────────────────────
            // 1 full + N incrementais diários
            [
                'name'             => '3 dias (1 Full + 2 Incrementais)',
                'days'             => 3,
                'is_full'          => false,
                'full_frequency'   => 'incremental',
                'change_rate'      => 10.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 1,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'name'             => '7 dias (1 Full + 6 Incrementais)',
                'days'             => 7,
                'is_full'          => false,
                'full_frequency'   => 'incremental',
                'change_rate'      => 10.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 2,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'name'             => '15 dias (1 Full + 14 Incrementais)',
                'days'             => 15,
                'is_full'          => false,
                'full_frequency'   => 'incremental',
                'change_rate'      => 10.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 3,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'name'             => '30 dias (1 Full + 29 Incrementais)',
                'days'             => 30,
                'is_full'          => false,
                'full_frequency'   => 'incremental',
                'change_rate'      => 10.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 4,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            // ── Full Diário ──────────────────────────────────────────
            [
                'name'             => 'Full Diário - 7 dias',
                'days'             => 7,
                'is_full'          => true,
                'full_frequency'   => 'daily',
                'change_rate'      => 0.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 5,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'name'             => 'Full Diário - 15 dias',
                'days'             => 15,
                'is_full'          => true,
                'full_frequency'   => 'daily',
                'change_rate'      => 0.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 6,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'name'             => 'Full Diário - 30 dias',
                'days'             => 30,
                'is_full'          => true,
                'full_frequency'   => 'daily',
                'change_rate'      => 0.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 7,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            // ── Full Semanal ─────────────────────────────────────────
            [
                'name'             => 'Full Semanal - 30 dias (4 Fulls + 26 Incrementais)',
                'days'             => 30,
                'is_full'          => false,
                'full_frequency'   => 'weekly',
                'change_rate'      => 10.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 8,
                'created_at'       => now(), 'updated_at' => now(),
            ],
            // ── Full Mensal ──────────────────────────────────────────
            [
                'name'             => 'Full Mensal - 30 dias (1 Full + 29 Incrementais)',
                'days'             => 30,
                'is_full'          => false,
                'full_frequency'   => 'monthly',
                'change_rate'      => 10.00,
                'compression_rate' => 40.00,
                'price_multiplier' => 1.00,
                'is_active'        => true,
                'sort_order'       => 9,
                'created_at'       => now(), 'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ ' . DB::table('backup_retention_options')->count() . ' opções de retenção inseridas!');
    }
}
