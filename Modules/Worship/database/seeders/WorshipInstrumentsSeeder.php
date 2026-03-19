<?php

namespace Modules\Worship\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Worship\App\Models\WorshipInstrument;
use Modules\Worship\App\Models\WorshipInstrumentCategory;

class WorshipInstrumentsSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Categories
        $categories = [
            ['name' => 'Harmonia', 'slug' => 'harmonia', 'color' => 'purple', 'icon' => 'fa-pro fa-solid fa-music'],
            ['name' => 'Melodia', 'slug' => 'melodia', 'color' => 'blue', 'icon' => 'fa-pro fa-solid fa-microphone'],
            ['name' => 'Percussão', 'slug' => 'percussao', 'color' => 'orange', 'icon' => 'fa-pro fa-solid fa-drum'],
            ['name' => 'Vocal', 'slug' => 'vocal', 'color' => 'pink', 'icon' => 'fa-pro fa-solid fa-users'],
            ['name' => 'Técnico', 'slug' => 'tecnico', 'color' => 'gray', 'icon' => 'fa-pro fa-solid fa-gear'],
        ];

        foreach ($categories as $cat) {
            WorshipInstrumentCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Seed Instruments
        $instruments = [
            ['name' => 'Vocal Soprano', 'slug' => 'vocal-soprano', 'icon' => 'fa-pro fa-solid fa-microphone', 'category' => 'vocal'],
            ['name' => 'Vocal Retorno', 'slug' => 'vocal-retorno', 'icon' => 'fa-pro fa-solid fa-microphone-lines', 'category' => 'vocal'],
            ['name' => 'Violão Aço', 'slug' => 'acoustic-guitar', 'icon' => 'fa-pro fa-solid fa-guitar', 'category' => 'harmonia'],
            ['name' => 'Guitarra Solo', 'slug' => 'electric-guitar', 'icon' => 'fa-pro fa-solid fa-guitar-electric', 'category' => 'harmonia'],
            ['name' => 'Baixo', 'slug' => 'bass', 'icon' => 'fa-pro fa-solid fa-wave-square', 'category' => 'harmonia'],
            ['name' => 'Bateria', 'slug' => 'drums', 'icon' => 'fa-pro fa-solid fa-drum', 'category' => 'percussao'],
            ['name' => 'Teclado/Piano', 'slug' => 'keys', 'icon' => 'fa-pro fa-solid fa-keyboard', 'category' => 'harmonia'],
            ['name' => 'Sonoplastia', 'slug' => 'audio-tech', 'icon' => 'fa-pro fa-solid fa-sliders', 'category' => 'tecnico'],
        ];

        foreach ($instruments as $instData) {
            $categorySlug = $instData['category'];
            unset($instData['category']);

            $category = WorshipInstrumentCategory::where('slug', $categorySlug)->first();

            WorshipInstrument::updateOrCreate(
                ['slug' => $instData['slug']],
                array_merge($instData, ['category_id' => $category?->id])
            );
        }
    }
}
