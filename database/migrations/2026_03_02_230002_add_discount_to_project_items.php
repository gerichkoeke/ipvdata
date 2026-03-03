<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar desconto às VMs
        if (Schema::hasTable('project_vms')) {
            Schema::table('project_vms', function (Blueprint $table) {
                if (!Schema::hasColumn('project_vms', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0);
                }
            });
        }

        // Adicionar desconto aos discos de VMs
        if (Schema::hasTable('project_vm_disks')) {
            Schema::table('project_vm_disks', function (Blueprint $table) {
                if (!Schema::hasColumn('project_vm_disks', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0);
                }
            });
        }

        // Adicionar desconto ao S3 Storage
        if (Schema::hasTable('project_s3_storage')) {
            Schema::table('project_s3_storage', function (Blueprint $table) {
                if (!Schema::hasColumn('project_s3_storage', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0);
                }
            });
        }

        // Adicionar desconto ao Backup Standalone
        if (Schema::hasTable('project_backup_standalone')) {
            Schema::table('project_backup_standalone', function (Blueprint $table) {
                if (!Schema::hasColumn('project_backup_standalone', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0);
                }
            });
        }

        // Adicionar desconto às Storage Services (caso exista)
        if (Schema::hasTable('project_storage_services')) {
            Schema::table('project_storage_services', function (Blueprint $table) {
                if (!Schema::hasColumn('project_storage_services', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0);
                }
            });
        }

        // Adicionar desconto à rede no projeto
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'network_discount_amount')) {
                    $table->decimal('network_discount_amount', 10, 2)->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'project_vms',
            'project_vm_disks',
            'project_s3_storage',
            'project_backup_standalone',
            'project_storage_services',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'discount_amount')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->dropColumn('discount_amount');
                });
            }
        }

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'network_discount_amount')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('network_discount_amount');
            });
        }
    }
};
