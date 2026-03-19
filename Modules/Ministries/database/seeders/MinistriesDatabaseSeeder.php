<?php

namespace Modules\Ministries\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Ministries\App\Models\Ministry;

class MinistriesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ministries = [
            [
                'name' => 'Formacao Pastoral',
                'description' => 'Trilhas de desenvolvimento para pastores e lideres em formacao ministerial.',
                'icon' => 'fa:book-bible',
                'color' => 'blue',
            ],
            [
                'name' => 'Discipulado e Mentoria',
                'description' => 'Acompanhamento de lideres e equipes com foco em maturidade espiritual.',
                'icon' => 'fa:users',
                'color' => 'indigo',
            ],
            [
                'name' => 'Louvor e Liturgia',
                'description' => 'Capacitacao ministerial para culto, adoracao e servico musical.',
                'icon' => 'fa:music',
                'color' => 'purple',
            ],
            [
                'name' => 'Missao e Evangelizacao',
                'description' => 'Frente ministerial para mobilizacao missionaria e evangelismo biblico.',
                'icon' => 'fa:bullhorn',
                'color' => 'green',
            ],
        ];

        foreach ($ministries as $data) {
            Ministry::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug' => Str::slug($data['name']),
                    'is_active' => true,
                    'requires_approval' => true,
                ])
            );
        }
    }
}
