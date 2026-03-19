<?php

namespace Modules\Bible\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BibleDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seeds do módulo Bible.
     *
     * - BibleBookPanoramaSeeder: panorama dos 66 livros (autor, data, tema)
     * - BiblePlanTemplatesSeeder: templates de planos (Batista / Cronológico / etc.)
     */
    public function run(): void
    {
        $this->call([
            BibleBookPanoramaSeeder::class,
            BiblePlanTemplatesSeeder::class,
        ]);
    }
}
