<?php

namespace Modules\Events\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventPriceRule;

class EventsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(EventTypesSeeder::class);

        // Get first user or create a default one
        $user = User::first();

        if (! $user) {
            $this->command->warn('Nenhum usuário encontrado. Crie um usuário primeiro.');

            return;
        }

        // Create example formation: Formação Pastoral Básica
        $event = Event::firstOrCreate(
            ['slug' => 'formacao-pastoral-basica-2026'],
            [
                'title' => 'Formação Pastoral Básica 2026',
                'description' => 'Fundamentos essenciais para candidatos ao ministério pastoral e pastores iniciantes.',
                'description_long' => 'Esta formação oferece uma base sólida nos aspectos fundamentais do ministério pastoral, incluindo hermenêutica, homilética, eclesiologia e cuidado pastoral. Ideal para candidatos ao ministério e pastores com pouca experiência.',
                'banner_path' => null,
                'start_date' => Carbon::now()->addMonths(1)->setTime(19, 0),
                'end_date' => Carbon::now()->addMonths(1)->addDays(3)->setTime(18, 0),
                'location' => 'Centro de Formação VEPL - São Paulo',
                'location_data' => [
                    'address' => 'Rua da Formação, 123',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'zipcode' => '01000-000',
                ],
                'delivery_mode' => 'presencial',
                'capacity' => 50,
                'min_participants' => 10,
                'max_per_registration' => 3,
                'status' => Event::STATUS_PUBLISHED,
                'visibility' => Event::VISIBILITY_MEMBERS,
                'requires_ordination' => false,
                'requires_ministry_experience' => false,
                'workload_hours' => 40,
                'difficulty_level' => Event::DIFFICULTY_INICIANTE,
                'issues_certificate' => true,
                'issues_continuing_education_credit' => true,
                'credit_hours' => 40,
                'is_free' => false,
                'base_price' => 350.00,
                'registration_start_date' => Carbon::now()->subWeeks(2),
                'registration_end_date' => Carbon::now()->addMonths(1)->subDays(7),
                'educational_objectives' => [
                    'Compreender os fundamentos bíblicos do ministério pastoral',
                    'Desenvolver habilidades básicas de pregação e ensino',
                    'Aprender princípios de liderança eclesiástica',
                    'Conhecer aspectos práticos da vida pastoral'
                ],
                'target_audience' => ['pastors', 'seminary_students', 'lay_leaders'],
                'materials_included' => [
                    'Apostila completa (200 páginas)',
                    'Bibliografia básica recomendada',
                    'Acesso a biblioteca digital',
                    'Certificado de participação'
                ],
                'contact_name' => 'Pr. João Silva',
                'contact_email' => 'formacao@vepl.org',
                'contact_phone' => '(11) 9999-8888',
                'contact_whatsapp' => '(11) 9999-8888',
                'form_fields' => [
                    [
                        'type' => 'text',
                        'label' => 'Igreja de origem',
                        'name' => 'home_church',
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Função na igreja',
                        'name' => 'church_role',
                        'options' => ['Membro', 'Líder', 'Diácono', 'Pastor Auxiliar', 'Pastor'],
                        'required' => true,
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Anos de experiência ministerial',
                        'name' => 'ministry_years',
                        'required' => false,
                    ],
                ],
                'created_by' => $user->id,
            ]
        );

        // Create price rules for pastoral formation
        $priceRules = [
            [
                'name' => 'Estudante de Seminário',
                'description' => 'Desconto para estudantes de seminário com comprovação',
                'rule_type' => 'ministry_level',
                'ministry_levels' => json_encode(['seminary_student']),
                'discount_type' => 'percentage',
                'discount_value' => 30.00,
                'override_price' => null,
                'priority' => 10,
                'is_active' => true,
                'auto_apply' => false,
                'requires_approval' => true,
                'required_documentation' => json_encode(['declaracao_matricula', 'carteira_estudante']),
            ],
            [
                'name' => 'Pastor Ordenado',
                'description' => 'Preço especial para pastores ordenados',
                'rule_type' => 'ordination_status',
                'requires_ordination' => true,
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'override_price' => null,
                'priority' => 20,
                'is_active' => true,
                'auto_apply' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Líder Leigo',
                'description' => 'Valor padrão para líderes leigos e diáconos',
                'rule_type' => 'ministry_level',
                'ministry_levels' => json_encode(['lay_leader', 'deacon']),
                'discount_type' => 'fixed_amount',
                'discount_value' => 0.00,
                'override_price' => 350.00,
                'priority' => 30,
                'is_active' => true,
                'auto_apply' => true,
                'requires_approval' => false,
            ],
        ];

        foreach ($priceRules as $ruleData) {
            EventPriceRule::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'name' => $ruleData['name'],
                ],
                array_merge($ruleData, ['event_id' => $event->id])
            );
        }

        $this->command->info('Evento exemplo criado com sucesso!');
        $this->command->info('Slug: '.$event->slug);
        $this->command->info('Título: '.$event->title);
        $this->command->info('Regras de preço criadas: '.count($priceRules));
    }
}
