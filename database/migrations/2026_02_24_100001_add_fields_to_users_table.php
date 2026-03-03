<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = Schema::getColumnListing('users');
            if (!in_array('avatar', $columns))           $table->string('avatar')->nullable()->after('email');
            if (!in_array('phone', $columns))            $table->string('phone', 20)->nullable()->after('avatar');
            if (!in_array('panel', $columns))            $table->enum('panel', ['admin', 'partner'])->default('partner')->after('phone');
            if (!in_array('partner_id', $columns))       $table->unsignedBigInteger('partner_id')->nullable()->after('panel');
            if (!in_array('is_active', $columns))        $table->boolean('is_active')->default(true)->after('partner_id');
            if (!in_array('mfa_enabled', $columns))      $table->boolean('mfa_enabled')->default(false)->after('is_active');
            if (!in_array('mfa_secret', $columns))       $table->string('mfa_secret')->nullable()->after('mfa_enabled');
            if (!in_array('mfa_confirmed_at', $columns)) $table->timestamp('mfa_confirmed_at')->nullable()->after('mfa_secret');
            if (!in_array('last_login_at', $columns))    $table->timestamp('last_login_at')->nullable()->after('mfa_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_intersect(
                ['avatar','phone','panel','partner_id','is_active','mfa_enabled','mfa_secret','mfa_confirmed_at','last_login_at'],
                Schema::getColumnListing('users')
            ));
        });
    }
};
