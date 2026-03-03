<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBackupContractsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_backup_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->unsignedInteger('machines');
            $table->unsignedBigInteger('total_disk_gb');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_backup_contracts');
    }
}
