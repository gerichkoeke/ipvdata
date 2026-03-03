<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de SKUs Microsoft
        Schema::create('ms_license_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('os_distribution_id')
                ->constrained('os_distributions')->restrictOnDelete();
            $table->string('name');
            $table->string('part_number')->nullable()
                ->comment('Part Number Microsoft ex: 9EM-00653');
            $table->enum('license_type', ['standard', 'datacenter', 'enterprise'])
                ->default('standard');
            $table->integer('cores_per_license')->default(16)
                ->comment('Mínimo de cores por pacote de compra');
            $table->integer('threshold_cores')->default(16)
                ->comment('Acima disso a licença fica no CNPJ do cliente');
            $table->boolean('sa_available')->default(true)
                ->comment('Tem Software Assurance disponível');
            $table->decimal('cost_price', 12, 2)->default(0)
                ->comment('Preço de custo por core para a empresa B');
            $table->decimal('sale_price', 12, 2)->default(0)
                ->comment('Preço de venda por core para o cliente');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Pool de licenças compradas pela empresa B
        Schema::create('ms_license_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sku_id')
                ->constrained('ms_license_skus')->restrictOnDelete();
            $table->string('invoice_number')->nullable()
                ->comment('Número da nota fiscal');
            $table->integer('purchased_cores')
                ->comment('Total de cores comprados');
            $table->decimal('cost_per_core', 12, 4)
                ->comment('Custo unitário por core pago');
            $table->integer('sa_years')->nullable()
                ->comment('Anos de Software Assurance: 1, 2 ou 3');
            $table->date('purchased_at');
            $table->date('sa_expires_at')->nullable()
                ->comment('Expiração do SA');
            $table->enum('status', ['active', 'expired', 'cancelled'])
                ->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Alocações do pool para VMs de clientes
        Schema::create('ms_license_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')
                ->constrained('ms_license_pools')->restrictOnDelete();
            $table->foreignId('customer_id')
                ->constrained('customers')->restrictOnDelete();
            $table->foreignId('project_vm_id')->nullable()
                ->constrained('project_vms')->nullOnDelete();
            $table->integer('allocated_cores');
            $table->date('allocated_at');
            $table->date('released_at')->nullable();
            $table->enum('status', ['active', 'released'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Licenças no CNPJ do próprio cliente (16+ cores / enterprise)
        Schema::create('ms_customer_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')->restrictOnDelete();
            $table->foreignId('sku_id')
                ->constrained('ms_license_skus')->restrictOnDelete();
            $table->foreignId('project_vm_id')->nullable()
                ->constrained('project_vms')->nullOnDelete();
            $table->integer('cores');
            $table->enum('license_modality', ['OEM', 'RETAIL', 'VOLUME'])->default('VOLUME');
            $table->string('part_number_purchased')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('tenant_id')->nullable()
                ->comment('Azure AD Tenant ID');
            $table->string('tenant_name')->nullable()
                ->comment('Nome do tenant Microsoft');
            $table->string('ms_customer_id')->nullable()
                ->comment('Microsoft Customer ID (MCID)');
            $table->integer('sa_years')->nullable();
            $table->date('purchased_at');
            $table->date('sa_expires_at')->nullable();
            $table->decimal('cost_per_core', 12, 4)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Adicionar campos Microsoft na tabela customers
        Schema::table('customers', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->after('notes')
                ->comment('Azure AD Tenant ID do cliente');
            $table->string('tenant_name')->nullable()->after('tenant_id')
                ->comment('Nome do tenant Microsoft do cliente');
            $table->string('ms_customer_id')->nullable()->after('tenant_name')
                ->comment('Microsoft Customer ID (MCID)');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['tenant_id', 'tenant_name', 'ms_customer_id']);
        });
        Schema::dropIfExists('ms_customer_licenses');
        Schema::dropIfExists('ms_license_allocations');
        Schema::dropIfExists('ms_license_pools');
        Schema::dropIfExists('ms_license_skus');
    }
};
