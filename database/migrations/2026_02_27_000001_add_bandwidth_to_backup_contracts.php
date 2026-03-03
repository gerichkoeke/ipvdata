<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddBandwidthToBackupContracts extends Migration {
    public function up() {
        Schema::table('customer_backup_contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('bandwidth_option_id')->nullable()->after('network_type_id');
        });
    }
    public function down() {
        Schema::table('customer_backup_contracts', function (Blueprint $table) {
            $table->dropColumn('bandwidth_option_id');
        });
    }
}
