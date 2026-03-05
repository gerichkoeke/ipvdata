<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('locale', 10)->default('pt_BR')->after('phone');
            $table->string('currency', 3)->default('BRL')->after('locale');
        });
    }
    public function down(): void {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['locale', 'currency']);
        });
    }
};
