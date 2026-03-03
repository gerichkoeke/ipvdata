<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Rede — configurada uma vez por projeto
            $table->foreignId('network_type_id')->nullable()->constrained('network_types')->nullOnDelete()->after('proposal_id');
            $table->foreignId('firewall_option_id')->nullable()->constrained('firewall_options')->nullOnDelete()->after('network_type_id');
            $table->foreignId('bandwidth_option_id')->nullable()->constrained('bandwidth_options')->nullOnDelete()->after('firewall_option_id');
            $table->integer('extra_public_ips')->default(0)->after('bandwidth_option_id');
            $table->decimal('extra_ip_price', 12, 2)->default(0)->after('extra_public_ips');
            $table->boolean('network_configured')->default(false)->after('extra_ip_price');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['network_type_id']);
            $table->dropForeign(['firewall_option_id']);
            $table->dropForeign(['bandwidth_option_id']);
            $table->dropColumn(['network_type_id', 'firewall_option_id', 'bandwidth_option_id', 'extra_public_ips', 'extra_ip_price', 'network_configured']);
        });
    }
};
