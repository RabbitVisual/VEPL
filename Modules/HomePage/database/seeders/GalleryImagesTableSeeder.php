<?php

namespace Modules\HomePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HomePage\App\Models\GalleryImage;

class GalleryImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Idempotente: insere apenas o que falta; nunca apaga o que já existe.
        $images = [
            [
                'title' => 'Formacao Pastoral Intensiva',
                'description' => 'Turma em laboratorio de interpretacao biblica e pregacao expositiva',
                'image_path' => 'gallery/sample1.jpg',
                'image_url' => null,
                'category' => 'formacoes',
                'content_type' => 'image',
                'formation_context' => 'pregacao_expositiva',
                'tags' => ['formacao', 'pastores', 'ensino'],
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
                'created_by' => 1,
            ],
            [
                'title' => 'Cerimonia de Certificacao VEPL',
                'description' => 'Entrega de certificados para lideres concluintes',
                'image_path' => 'gallery/sample2.jpg',
                'image_url' => null,
                'category' => 'certificacoes',
                'content_type' => 'image',
                'formation_context' => 'certificacao',
                'tags' => ['certificacao', 'graduacao'],
                'is_active' => true,
                'order' => 2,
                'created_by' => 1,
            ],
            [
                'title' => 'Workshop de Lideranca Ministerial',
                'description' => 'Dinamiccas de gestao de equipes e cuidado pastoral',
                'image_path' => 'gallery/sample3.jpg',
                'image_url' => null,
                'category' => 'workshops',
                'content_type' => 'image',
                'formation_context' => 'lideranca',
                'tags' => ['lideranca', 'ministerios'],
                'is_active' => true,
                'order' => 3,
                'created_by' => 1,
            ],
            [
                'title' => 'Mesa de Mentoria Pastoral',
                'description' => 'Encontro de acompanhamento entre mentores e lideres locais',
                'image_path' => 'gallery/sample4.jpg',
                'image_url' => null,
                'category' => 'mentoria',
                'content_type' => 'image',
                'formation_context' => 'mentoria_pastoral',
                'tags' => ['mentoria', 'pastoral'],
                'is_active' => true,
                'order' => 4,
                'created_by' => 1,
            ],
            [
                'title' => 'Conferencia de Lideres Batista',
                'description' => 'Plenarias e paineis sobre saude ministerial e missao',
                'image_path' => 'gallery/sample5.jpg',
                'image_url' => null,
                'category' => 'encontros',
                'content_type' => 'image',
                'formation_context' => 'conferencia',
                'tags' => ['conferencia', 'networking'],
                'is_active' => true,
                'order' => 5,
                'created_by' => 1,
            ],
            [
                'title' => 'Networking entre Pastores',
                'description' => 'Conexao entre lideres para cooperacao e intercambio de experiencias',
                'image_path' => 'gallery/sample6.jpg',
                'image_url' => null,
                'category' => 'networking',
                'content_type' => 'image',
                'formation_context' => 'networking',
                'tags' => ['pastores', 'lideres'],
                'is_active' => true,
                'order' => 6,
                'created_by' => 1,
            ],
            [
                'title' => 'Laboratorio de Exposicao Biblica',
                'description' => 'Aula pratica com feedback de pregacao',
                'image_path' => 'gallery/sample7.jpg',
                'image_url' => null,
                'category' => 'formacoes',
                'content_type' => 'image',
                'formation_context' => 'exposicao_biblica',
                'tags' => ['biblia', 'pregacao'],
                'is_active' => true,
                'order' => 7,
                'created_by' => 1,
            ],
            [
                'title' => 'Imersao de Planejamento Ministerial',
                'description' => 'Times pastorais construindo planos e indicadores para o ano ministerial',
                'image_path' => 'gallery/sample8.jpg',
                'image_url' => null,
                'category' => 'planejamento',
                'content_type' => 'image',
                'formation_context' => 'planejamento',
                'tags' => ['planejamento', 'estrategia'],
                'is_active' => true,
                'order' => 8,
                'created_by' => 1,
            ],
        ];

        foreach ($images as $img) {
            GalleryImage::firstOrCreate(
                ['title' => $img['title'], 'category' => $img['category'], 'created_by' => $img['created_by']],
                $img
            );
        }
    }
}
