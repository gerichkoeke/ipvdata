<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            // distributor_id já existe, só adicionar se não existir
            if (!Schema::hasColumn('partners', 'distributor_id')) {
                $table->foreignId('distributor_id')->nullable()->after('id')->constrained('distributors')->nullOnDelete();
            }
            if (!Schema::hasColumn('partners', 'currency')) {
                $table->string('currency', 3)->default('BRL');
            }
            if (!Schema::hasColumn('partners', 'locale')) {
                $table->string('locale', 10)->default('pt_BR');
            }
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            if (Schema::hasColumn('partners', 'distributor_id')) {
                $table->dropForeign(['distributor_id']);
                $table->dropColumn('distributor_id');
            }
            if (Schema::hasColumn('partners', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('partners', 'locale')) {
                $table->dropColumn('locale');
            }
        });
    }
};
