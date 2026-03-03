<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_software_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('edition')->nullable()
                ->comment('Community, Essentials, Universal, Enterprise Plus');
            $table->enum('license_model', ['per_vm', 'per_socket', 'per_tb'])
                ->default('per_vm')
                ->comment('Modelo de licenciamento');
            $table->decimal('price_per_unit', 12, 2)->default(0)
                ->comment('Preço por VM/socket/TB por mês');
            $table->integer('included_units')->default(0)
                ->comment('Unidades inclusas na licença base (ex: Essentials = 10 VMs)');
            $table->boolean('has_agent')->default(false)
                ->comment('Tem agente para backup standalone (físico/cloud)');
            $table->decimal('price_per_agent', 12, 2)->default(0)
                ->comment('Preço do agente por mês (para backup standalone)');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Adicionar backup_software_id nas tabelas de VM e backup standalone
        Schema::table('project_vms', function (Blueprint $table) {
            $table->foreignId('backup_software_id')
                ->nullable()
                ->after('backup_retention_id')
                ->constrained('backup_software_options')
                ->nullOnDelete();
            $table->decimal('price_backup_software', 12, 2)
                ->default(0)
                ->after('backup_storage_gb')
                ->comment('Custo da licença de backup software por mês');
        });

        Schema::table('project_backup_standalone', function (Blueprint $table) {
            $table->foreignId('backup_software_id')
                ->nullable()
                ->after('backup_retention_id')
                ->constrained('backup_software_options')
                ->nullOnDelete();
            $table->decimal('price_backup_software', 12, 2)
                ->default(0)
                ->after('price_per_gb');
        });
    }

    public function down(): void
    {
        Schema::table('project_backup_standalone', function (Blueprint $table) {
            $table->dropForeign(['backup_software_id']);
            $table->dropColumn(['backup_software_id', 'price_backup_software']);
        });
        Schema::table('project_vms', function (Blueprint $table) {
            $table->dropForeign(['backup_software_id']);
            $table->dropColumn(['backup_software_id', 'price_backup_software']);
        });
        Schema::dropIfExists('backup_software_options');
    }
};
