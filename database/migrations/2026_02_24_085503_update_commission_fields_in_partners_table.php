<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            // Remover coluna antiga
            $table->dropColumn('commission_type');

            // Novas colunas
            $table->enum('commission_model', ['fixed', 'variable'])
                ->default('fixed')
                ->after('proposal_terms')
                ->comment('fixed = IPVDATA define; variable = parceiro define na proposta');

            $table->decimal('commission_rate', 5, 2)
                ->default(20.00)
                ->after('commission_model')
                ->comment('Percentual fixo (usado quando model=fixed)');

            $table->decimal('commission_min', 5, 2)
                ->default(0.00)
                ->after('commission_rate')
                ->comment('Percentual mínimo permitido (usado quando model=variable)');

            $table->decimal('commission_max', 5, 2)
                ->default(100.00)
                ->after('commission_min')
                ->comment('Percentual máximo permitido (usado quando model=variable)');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['commission_model', 'commission_rate', 'commission_min', 'commission_max']);
            $table->enum('commission_type', ['fixed', 'percentage'])->default('percentage');
        });
    }
};
