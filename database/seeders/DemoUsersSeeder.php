<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Cria usuários de demonstração para ambiente de desenvolvimento.
     * Idempotente via updateOrCreate.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $memberRole = Role::where('slug', 'membro')->first();

        if (! $adminRole || ! $memberRole) {
            $this->command->warn('⚠️  Roles não encontradas. Execute as migrations primeiro.');

            return;
        }

        // ── Administrador ──────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin Demo',
                'first_name' => 'Admin',
                'last_name' => 'Demo',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin: admin@demo.com / admin123');

        // ── Pastor / Líder (Membro) ────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'pastor@demo.com'],
            [
                'name' => 'Pastor Demo',
                'first_name' => 'João',
                'last_name' => 'Demo',
                'password' => Hash::make('pastor123'),
                'role_id' => $memberRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
                'cpf' => '999.999.999-99',
                'date_of_birth' => '1975-03-20',
                'gender' => 'M',
                'marital_status' => 'casado',
                'phone' => '(75) 3234-5678',
                'cellphone' => '(75) 9 9876-5432',
                'address' => 'Rua do Ministério',
                'address_number' => '100',
                'neighborhood' => 'Centro',
                'city' => 'Salvador',
                'state' => 'BA',
                'zip_code' => '40000-000',
                'title' => 'Pastor',
                'is_ordained' => true,
                'ordination_date' => '2005-06-15',
                'ministry_start_date' => '2000-01-01',
                'affiliated_church' => 'Primeira Igreja Batista Demo',
                'baptist_convention' => 'CBB',
                'theological_education' => 'Bacharel em Teologia',
                'is_baptized' => true,
                'baptism_date' => '1995-04-10',
                'profession' => 'Pastor',
                'education_level' => 'superior',
            ]
        );

        $this->command->info('✅ Pastor: pastor@demo.com / pastor123');
    }
}
