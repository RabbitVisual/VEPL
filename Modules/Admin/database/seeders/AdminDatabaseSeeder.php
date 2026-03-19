<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Seed de produção do módulo Admin.
     *
     * Popula as configurações padrão do sistema (idempotente).
     */
    public function run(): void
    {
        $this->command->info('⚙️  Seeding configurações do sistema (settings)...');

        $defaults = [
            // ── Geral ──────────────────────────────────────────────────────────
            ['key' => 'site_name',        'value' => 'Escola de Pastores e Líderes VEPL', 'type' => 'string',  'group' => 'general',    'description' => 'Nome da instituição'],
            ['key' => 'site_description', 'value' => 'Formando líderes para o Reino de Deus',  'type' => 'text',    'group' => 'general',    'description' => 'Descrição/subtítulo'],
            ['key' => 'site_email',       'value' => 'contato@vepl.com.br',                   'type' => 'string',  'group' => 'general',    'description' => 'E-mail institucional'],
            ['key' => 'site_phone',       'value' => '(75) 0000-0000',                        'type' => 'string',  'group' => 'general',    'description' => 'Telefone de contato'],
            ['key' => 'site_address',     'value' => 'Bahia, Brasil',                         'type' => 'text',    'group' => 'general',    'description' => 'Endereço da instituição'],
            ['key' => 'maintenance_mode', 'value' => '0',                                     'type' => 'boolean', 'group' => 'general',    'description' => 'Modo de manutenção ativo?'],

            // ── Aparência ──────────────────────────────────────────────────────
            ['key' => 'logo_path',        'value' => 'storage/image/logo_oficial.png',        'type' => 'file',    'group' => 'appearance', 'description' => 'Logo oficial da instituição'],
            ['key' => 'logo_icon_path',   'value' => 'storage/image/logo_icon.png',           'type' => 'file',    'group' => 'appearance', 'description' => 'Ícone/Favicon'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ ' . count($defaults) . ' configurações registradas.');
    }
}
