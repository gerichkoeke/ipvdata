<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trade_name')->nullable();
            $table->string('document')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('contact_name')->nullable();
            $table->decimal('commission_pct', 5, 2)->default(10.00);
            $table->string('currency', 3)->default('BRL');
            $table->string('locale', 10)->default('pt_BR');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributors');
    }
};
