<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('partner_commission_percentage', 5, 2)->nullable()->after('partner_id');
            $table->decimal('global_discount_amount', 10, 2)->default(0)->after('partner_commission_percentage');
            $table->string('global_discount_currency', 3)->nullable()->after('global_discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'partner_commission_percentage',
                'global_discount_amount',
                'global_discount_currency'
            ]);
        });
    }
};
