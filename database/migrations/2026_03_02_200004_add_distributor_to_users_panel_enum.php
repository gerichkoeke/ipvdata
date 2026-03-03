<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Altera o ENUM adicionando 'distributor'
        DB::statement("ALTER TABLE users MODIFY COLUMN panel ENUM('admin','partner','distributor') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN panel ENUM('admin','partner') NULL");
    }
};
