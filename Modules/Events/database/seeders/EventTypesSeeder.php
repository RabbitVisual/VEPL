<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Events\App\Models\EventType;

class EventTypesSeeder extends Seeder
{
    public function run(): void
    {
        // Os tipos já são inseridos na própria migration com seeds embutidos
        $this->command->info('Tipos de formação VEPL carregados via migration.');
    }
}
