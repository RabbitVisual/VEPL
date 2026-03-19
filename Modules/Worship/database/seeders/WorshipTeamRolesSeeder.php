<?php

namespace Modules\Worship\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Worship\App\Models\WorshipTeamRole;
use Modules\Worship\App\Models\WorshipEquipment;

class WorshipTeamRolesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'Líder de Louvor', 'color' => 'red', 'description' => 'Responsável por conduzir a ministração e o setlist.'],
            ['name' => 'Backing Vocal', 'color' => 'pink', 'description' => 'Suporte vocal e harmonização.'],
            ['name' => 'Instrumentista', 'color' => 'blue', 'description' => 'Execução instrumental conforme escala.'],
            ['name' => 'Técnico de Som', 'color' => 'gray', 'description' => 'Operação da mesa e monitoramento.'],
            ['name' => 'Projeção/Media', 'color' => 'black', 'description' => 'Operação de slides e letras.'],
        ];

        foreach ($roles as $role) {
            WorshipTeamRole::updateOrCreate(['name' => $role['name']], $role);
        }

        // 2. Equipment (Example inventory)
        $roleInst = WorshipTeamRole::where('name', 'Instrumentista')->first();
        $roleSound = WorshipTeamRole::where('name', 'Técnico de Som')->first();

        $equipments = [
            [
                'name' => 'Microfone Shure SM58 #1',
                'worship_team_role_id' => $roleInst?->id,
                'status' => 'active',
                'serial_number' => 'SH-58-001',
                'notes' => 'Cabo XLR de 5m incluso.'
            ],
            [
                'name' => 'Direct Box Whirlwind',
                'worship_team_role_id' => $roleInst?->id,
                'status' => 'active',
                'serial_number' => 'DI-WW-042',
            ],
            [
                'name' => 'Mesa de Som Behringer X32',
                'worship_team_role_id' => $roleSound?->id,
                'status' => 'active',
                'notes' => 'Revisada em Jan/2026.'
            ],
            [
                'name' => 'IPad para Retorno #1',
                'worship_team_role_id' => $roleInst?->id,
                'status' => 'maintenance',
                'notes' => 'Tela trincada, aguardando conserto.'
            ],
        ];

        foreach ($equipments as $equip) {
            WorshipEquipment::updateOrCreate(['name' => $equip['name']], $equip);
        }
    }
}
