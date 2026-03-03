<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('network_types', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0)->after('default_ips')
                  ->comment('Preço mensal do tipo de rede');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('lan_to_lan_address')->nullable()->after('network_type_id')
                  ->comment('Endereço/IP destino para viabilidade LAN-to-LAN');
            $table->string('lan_to_lan_viability')->nullable()->after('lan_to_lan_address')
                  ->comment('Resultado da consulta de viabilidade');
            $table->timestamp('lan_to_lan_checked_at')->nullable()->after('lan_to_lan_viability');
        });
    }

    public function down(): void
    {
        Schema::table('network_types', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['lan_to_lan_address', 'lan_to_lan_viability', 'lan_to_lan_checked_at']);
        });
    }
};
