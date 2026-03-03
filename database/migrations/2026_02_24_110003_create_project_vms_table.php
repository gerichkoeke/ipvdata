<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_vms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            // Identificação
            $table->string('name');           // nome da VM ex: "VM-Web-01"
            $table->text('description')->nullable();

            // Recursos computacionais
            $table->integer('cpu_cores');     // quantidade de vCPUs
            $table->integer('ram_gb');        // RAM em GB
            $table->integer('disk_os_gb');    // disco do SO em GB
            $table->foreignId('disk_os_type_id')->constrained('disk_types'); // tipo do disco OS

            // Sistema Operacional
            $table->foreignId('os_distribution_id')->constrained('os_distributions');

            // Remote Desktop (só Windows)
            $table->foreignId('remote_desktop_type_id')->nullable()->constrained('remote_desktop_types')->nullOnDelete();
            $table->foreignId('rds_license_mode_id')->nullable()->constrained('rds_license_modes')->nullOnDelete();
            $table->integer('rds_license_qty')->default(0);

            // Endpoint Security
            $table->foreignId('endpoint_security_id')->nullable()->constrained('endpoint_security_options')->nullOnDelete();

            // Backup
            $table->boolean('has_backup')->default(false);
            $table->foreignId('backup_retention_id')->nullable()->constrained('backup_retention_options')->nullOnDelete();
            $table->decimal('backup_storage_gb', 12, 2)->default(0)
                ->comment('calculado automaticamente: 50% de todos os discos');

            // Preços calculados
            $table->decimal('price_cpu', 12, 2)->default(0);
            $table->decimal('price_ram', 12, 2)->default(0);
            $table->decimal('price_disk_os', 12, 2)->default(0);
            $table->decimal('price_os_license', 12, 2)->default(0);
            $table->decimal('price_rds', 12, 2)->default(0);
            $table->decimal('price_endpoint', 12, 2)->default(0);
            $table->decimal('price_backup', 12, 2)->default(0);
            $table->decimal('price_total_monthly', 12, 2)->default(0);

            $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Discos adicionais de cada VM
        Schema::create('project_vm_disks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_vm_id')->constrained('project_vms')->cascadeOnDelete();
            $table->foreignId('disk_type_id')->constrained('disk_types');
            $table->integer('size_gb');
            $table->decimal('price', 12, 2)->default(0); // calculado: size_gb * price_per_gb
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_vm_disks');
        Schema::dropIfExists('project_vms');
    }
};
