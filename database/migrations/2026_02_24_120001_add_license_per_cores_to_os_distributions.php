<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('os_distributions', function (Blueprint $table) {
            // Preço por core (Windows Server é licenciado por core)
            $table->boolean('license_per_core')->default(false)->after('requires_license')
                ->comment('Se true, o preço é multiplicado pelo número de cores da VM');
            $table->integer('min_cores')->default(0)->after('license_per_core')
                ->comment('Mínimo de cores para licenciamento (Windows Server = 8 cores mínimo por processador)');
        });
    }

    public function down(): void
    {
        Schema::table('os_distributions', function (Blueprint $table) {
            $table->dropColumn(['license_per_core', 'min_cores']);
        });
    }
};
