<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar campos de cálculo nas opções de retenção
        Schema::table('backup_retention_options', function (Blueprint $table) {
            $table->enum('full_frequency', ['incremental', 'daily', 'weekly', 'monthly'])
                ->default('incremental')
                ->after('is_full')
                ->comment('incremental=1 full + N incr | daily=full todo dia | weekly=full semanal | monthly=full mensal');
            $table->decimal('change_rate', 5, 2)->default(10.00)
                ->after('full_frequency')
                ->comment('Taxa de mudança diária % para incrementais');
            $table->decimal('compression_rate', 5, 2)->default(40.00)
                ->after('change_rate')
                ->comment('Taxa de compressão % do Veeam (padrão 40%)');
        });

        // Simplificar backup_software: remover o que não usamos (community, agent)
        // Adicionar: is_per_vm (licença por VM) e billing_cycle
        Schema::table('backup_software_options', function (Blueprint $table) {
            $table->enum('billing_cycle', ['monthly', 'yearly'])
                ->default('monthly')
                ->after('price_per_unit');
        });
    }

    public function down(): void
    {
        Schema::table('backup_software_options', function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });
        Schema::table('backup_retention_options', function (Blueprint $table) {
            $table->dropColumn(['full_frequency', 'change_rate', 'compression_rate']);
        });
    }
};
