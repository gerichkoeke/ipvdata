<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomerBackupContractsTable extends Migration
{
    public function up()
    {
        Schema::table('customer_backup_contracts', function (Blueprint $table) {
            $table->foreignId('network_type_id')->nullable()->constrained()->nullOnDelete()->after('customer_id');
            $table->foreignId('retention_id')->nullable()->constrained('backup_retention_options')->nullOnDelete()->after('total_disk_gb');
            $table->foreignId('backup_software_id')->nullable()->constrained('backup_software_options')->nullOnDelete()->after('retention_id');
            $table->json('machines_detail')->nullable()->after('backup_software_id');
            $table->decimal('monthly_value', 10, 2)->default(0)->after('machines_detail');
        });
    }

    public function down()
    {
        Schema::table('customer_backup_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('network_type_id');
            $table->dropConstrainedForeignId('retention_id');
            $table->dropConstrainedForeignId('backup_software_id');
            $table->dropColumn(['machines_detail', 'monthly_value']);
        });
    }
}
