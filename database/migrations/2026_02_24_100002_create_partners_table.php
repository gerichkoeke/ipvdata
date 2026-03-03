<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('trade_name')->nullable();
            $table->string('cnpj', 18)->nullable()->unique();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->string('logo')->nullable();
            $table->string('proposal_header_color', 7)->default('#1e40af');
            $table->string('proposal_footer_text')->nullable();
            $table->text('proposal_terms')->nullable();
            $table->enum('commission_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('commission_value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
