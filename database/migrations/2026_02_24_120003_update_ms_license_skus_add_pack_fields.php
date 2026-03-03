<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ms_license_skus', function (Blueprint $table) {
            // Pack de cores (2-Core Pack, 8-Core Pack)
            $table->integer('pack_size')->default(2)->after('cores_per_license')
                ->comment('Tamanho do pack: 2 ou 8 cores por pack');

            // Período de cobrança
            $table->enum('billing_period', ['monthly', '1year', '3year'])
                ->default('1year')->after('pack_size');

            // CAL RDS vinculada ao SKU
            $table->boolean('is_cal')->default(false)->after('billing_period')
                ->comment('Se true, é uma CAL RDS (por user/device)');
            $table->enum('cal_type', ['user', 'device'])->nullable()->after('is_cal');
        });

        // Adicionar Windows 2025 nos SKUs
    }

    public function down(): void
    {
        Schema::table('ms_license_skus', function (Blueprint $table) {
            $table->dropColumn(['pack_size','billing_period','is_cal','cal_type']);
        });
    }
};
