<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Armazenamento S3 standalone
        Schema::create('project_s3_storage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name')->default('S3 Storage');
            $table->enum('unit', ['GB', 'TB'])->default('GB');
            $table->decimal('quantity', 12, 2);            // quantidade em GB ou TB
            $table->decimal('quantity_gb', 12, 2);         // sempre em GB para cálculo
            $table->decimal('price_per_gb', 12, 4)->default(0);
            $table->decimal('price_total_monthly', 12, 2)->default(0);
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Backup standalone (sem VM — via VPN/Wireguard)
        Schema::create('project_backup_standalone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name')->default('Backup Standalone');

            // Conectividade
            $table->enum('connection_type', ['vpc', 'wireguard'])->default('vpc');

            // Configuração
            $table->integer('vm_count');       // quantidade de máquinas externas
            $table->foreignId('backup_retention_id')->constrained('backup_retention_options');

            // Armazenamento calculado
            $table->decimal('storage_per_vm_gb', 12, 2);  // estimativa por VM
            $table->decimal('total_storage_gb', 12, 2);   // calculado: vm_count * storage_per_vm * 50%
            $table->decimal('price_per_gb', 12, 4)->default(0);
            $table->decimal('price_total_monthly', 12, 2)->default(0);

            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela de preços de recursos (CPU/RAM/IP por unidade)
        Schema::create('resource_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type'); // cpu_core, ram_gb, public_ip, s3_gb
            $table->string('name');          // descrição legível
            $table->decimal('price', 12, 4); // preço por unidade/mês
            $table->string('unit');          // vCore, GB, IP, GB
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_pricing');
        Schema::dropIfExists('project_backup_standalone');
        Schema::dropIfExists('project_s3_storage');
    }
};
