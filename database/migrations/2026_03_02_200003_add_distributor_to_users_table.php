<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'distributor_id')) {
                $table->foreignId('distributor_id')->nullable()->after('partner_id')->constrained('distributors')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'distributor_id')) {
                $table->dropForeign(['distributor_id']);
                $table->dropColumn('distributor_id');
            }
        });
    }
};
