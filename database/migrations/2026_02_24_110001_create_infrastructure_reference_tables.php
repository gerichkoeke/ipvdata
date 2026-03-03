<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tipos de rede do projeto
        Schema::create('network_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // VPC, Firewall, LAN-to-LAN
            $table->string('slug')->unique(); // vpc, firewall, lan-to-lan
            $table->boolean('has_public_ip')->default(true);
            $table->integer('default_ips')->default(1); // quantos IPs já inclusos
            $table->boolean('requires_firewall')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Opções de firewall
        Schema::create('firewall_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Fortinet, Sophos, Pfsense, OpnSense
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2)->default(0); // preço mensal da licença
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Famílias de OS
        Schema::create('os_families', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Linux, Windows
            $table->string('slug')->unique(); // linux, windows
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Distribuições de OS
        Schema::create('os_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('os_family_id')->constrained('os_families')->cascadeOnDelete();
            $table->string('name');            // Ubuntu 22.04 LTS, Windows Server 2022
            $table->string('version')->nullable();
            $table->decimal('price', 12, 2)->default(0); // custo de licença se houver
            $table->boolean('requires_license')->default(false); // Windows = true
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Tipos de remote desktop (só para Windows)
        Schema::create('remote_desktop_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // RDS Microsoft, TSPLUS
            $table->string('slug')->unique();
            $table->boolean('has_license_modes')->default(true); // se tem por device/usuário
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Modos de licença RDS (por device ou por usuário)
        Schema::create('rds_license_modes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_desktop_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');            // Por Device, Por Usuário
            $table->string('slug')->unique();  // per-device, per-user
            $table->decimal('price_per_unit', 12, 2)->default(0); // preço por unidade/mês
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tipos de disco
        Schema::create('disk_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // NVME, SSD, HDD
            $table->string('slug')->unique();  // nvme, ssd, hdd
            $table->decimal('price_per_gb', 12, 4)->default(0); // preço por GB/mês
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Segurança endpoint
        Schema::create('endpoint_security_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // SentinelOne
            $table->string('slug')->unique();
            $table->decimal('price_per_vm', 12, 2)->default(0); // preço por VM/mês
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Opções de retenção de backup
        Schema::create('backup_retention_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // 3 dias, 7 dias, 15 dias, 30 dias, Full
            $table->integer('days')->nullable(); // null = full
            $table->boolean('is_full')->default(false);
            $table->decimal('price_multiplier', 5, 2)->default(1.00); // multiplicador de custo
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Tamanhos de banda
        Schema::create('bandwidth_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // 10MB, 100MB, 1000MB
            $table->integer('mbps');           // valor em Mbps
            $table->decimal('price', 12, 2)->default(0); // preço mensal
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandwidth_options');
        Schema::dropIfExists('backup_retention_options');
        Schema::dropIfExists('endpoint_security_options');
        Schema::dropIfExists('disk_types');
        Schema::dropIfExists('rds_license_modes');
        Schema::dropIfExists('remote_desktop_types');
        Schema::dropIfExists('os_distributions');
        Schema::dropIfExists('os_families');
        Schema::dropIfExists('firewall_options');
        Schema::dropIfExists('network_types');
    }
};
