<?php

namespace Modules\HomePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HomePage\App\Models\Testimonial;

class TestimonialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Idempotente: insere apenas o que falta; nunca apaga o que já existe.
        $testimonials = [
            [
                'name' => 'Pr. Marcos Almeida',
                'photo' => null,
                'testimonial' => 'A trilha de Lideranca Pastoral da VEPL reorganizou minha rotina ministerial e melhorou o cuidado com minha igreja local.',
                'position' => 'Pastor Titular',
                'ministerial_title' => 'Pastor',
                'formation_completed' => 'Formacao Pastoral Basica',
                'church_affiliation' => 'Igreja Batista Esperanca',
                'testimonial_type' => 'written',
                'ministry_level' => 'pastor',
                'is_verified' => true,
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
                'created_by' => 1,
            ],
            [
                'name' => 'Pra. Helena Souza',
                'photo' => null,
                'testimonial' => 'A mentoria em exegese e pregacao expositiva elevou a qualidade dos estudos biblicos e da formacao de lideres em minha congregacao.',
                'position' => 'Lider Ministerial',
                'ministerial_title' => 'Lider de Ensino',
                'formation_completed' => 'Mentoria de Exegese Biblica',
                'church_affiliation' => 'Igreja Batista Rocha Viva',
                'testimonial_type' => 'written',
                'ministry_level' => 'lider',
                'is_verified' => true,
                'is_active' => true,
                'order' => 2,
                'created_by' => 1,
            ],
            [
                'name' => 'Diac. Rafael Nunes',
                'photo' => null,
                'testimonial' => 'Conclui a formacao em Lideranca de Pequenos Grupos e hoje estruturamos acompanhamento pastoral com indicadores claros de discipulado.',
                'position' => 'Diacono',
                'ministerial_title' => 'Diacono',
                'formation_completed' => 'Lideranca de Pequenos Grupos',
                'church_affiliation' => 'Igreja Batista Nova Alianca',
                'testimonial_type' => 'written',
                'ministry_level' => 'diacono',
                'is_verified' => true,
                'is_active' => true,
                'order' => 3,
                'created_by' => 1,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(
                ['name' => $t['name'], 'position' => $t['position']],
                $t
            );
        }
    }
}
