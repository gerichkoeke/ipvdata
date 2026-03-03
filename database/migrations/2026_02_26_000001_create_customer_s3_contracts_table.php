<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerS3ContractsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_s3_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('size_gb');
            $table->decimal('price_per_gb', 8, 4)->default(0.10);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_s3_contracts');
    }
}
