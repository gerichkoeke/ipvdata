<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar roles
        $roles = ['super_admin', 'partner_admin', 'partner_user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Criar usuário Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@ipvdata.com.br'],
            [
                'name'      => 'Super Admin',
                'password'  => bcrypt('Admin@123456'),
                'panel'     => 'admin',
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');

        $this->command->info('');
        $this->command->info('✅ Usuário admin criado com sucesso!');
        $this->command->info('   E-mail : admin@ipvdata.com.br');
        $this->command->info('   Senha  : Admin@123456');
        $this->command->info('   URL    : https://admin.ipvdata.com.br/admin');
        $this->command->info('');

        $this->call(ResourcePricingSeeder::class);
    }
}
