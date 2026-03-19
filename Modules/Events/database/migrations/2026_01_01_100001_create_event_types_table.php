<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tipos de Formações e Eventos Pastorais (VEPL).
     * 
     * Base para categorização de formações ministeriais, congressos, retiros,
     * mentoria pastoral, seminários bíblicos e eventos educacionais específicos
     * para pastores, líderes eclesiásticos e profissionais do ministério cristão.
     */
    public function up(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            // ── Identificação e Categorização ─────────────────────────────────────
            $table->id();
            $table->string('name')->comment('Nome do tipo de formação');
            $table->string('slug')->unique()->comment('Identificador único para URLs');
            $table->text('description')->nullable()->comment('Descrição detalhada do tipo de formação');

            // ── Apresentação Visual ───────────────────────────────────────────────
            $table->string('icon', 100)->default('calendar')->comment('Ícone FontAwesome para UI');
            $table->string('color', 30)->default('#6B7280')->comment('Cor hexadecimal para identificação visual');
            
            // ── Configuração Educacional ──────────────────────────────────────────
            $table->enum('educational_level', ['basico', 'intermediario', 'avancado', 'especializado'])->default('basico')->comment('Nível educacional da formação');
            $table->integer('typical_duration_hours')->nullable()->comment('Duração típica em horas');
            $table->boolean('requires_ordination')->default(false)->comment('Exige ordenação pastoral');
            $table->boolean('continuing_education_credit')->default(false)->comment('Oferece crédito de educação continuada');

            // ── Público Alvo Pastoral ─────────────────────────────────────────────
            $table->json('target_ministries')->nullable()->comment('Ministérios alvo: ["pastoral", "diaconal", "musical", "jovens", "infantil"]');
            $table->boolean('open_to_laity')->default(true)->comment('Aberto a membros leigos');
            
            // ── Organização e Apresentação ────────────────────────────────────────
            $table->unsignedSmallInteger('display_order')->default(99)->comment('Ordem de exibição no frontend');
            $table->boolean('is_active')->default(true)->comment('Tipo ativo/disponível para uso');
            
            $table->timestamps();

            // ── Índices para Performance ──────────────────────────────────────────
            $table->index('slug');
            $table->index(['is_active', 'display_order']);
            $table->index('educational_level');
        });

        // ── Seeds Iniciais para Tipos de Formação VEPL ───────────────────────────
        $this->seedFormationTypes();
    }

    /**
     * Tipos de formação específicos para VEPL
     */
    private function seedFormationTypes(): void
    {
        $types = [
            [
                'name' => 'Formação Pastoral Básica',
                'slug' => 'formacao-pastoral-basica',
                'description' => 'Formação fundamental para pastores iniciantes e candidatos ao ministério pastoral',
                'icon' => 'user-graduate',
                'color' => '#4F46E5',
                'educational_level' => 'basico',
                'typical_duration_hours' => 40,
                'requires_ordination' => false,
                'continuing_education_credit' => true,
                'target_ministries' => json_encode(['pastoral']),
                'open_to_laity' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Mentoria de Liderança',
                'slug' => 'mentoria-lideranca',
                'description' => 'Acompanhamento personalizado para desenvolvimento de líderes eclesiásticos',
                'icon' => 'chalkboard-user',
                'color' => '#059669',
                'educational_level' => 'intermediario',
                'typical_duration_hours' => 20,
                'requires_ordination' => false,
                'continuing_education_credit' => true,
                'target_ministries' => json_encode(['pastoral', 'diaconal', 'jovens']),
                'open_to_laity' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Conferência de Pastores',
                'slug' => 'conferencia-pastores',
                'description' => 'Encontro anual de pastores para discussão de temas ministeriais contemporâneos',
                'icon' => 'microphone',
                'color' => '#2563EB',
                'educational_level' => 'avancado',
                'typical_duration_hours' => 16,
                'requires_ordination' => true,
                'continuing_education_credit' => true,
                'target_ministries' => json_encode(['pastoral']),
                'open_to_laity' => false,
                'display_order' => 3,
            ],
            [
                'name' => 'Retiro Espiritual',
                'slug' => 'retiro-espiritual',
                'description' => 'Momentos de reflexão espiritual e renovação ministerial',
                'icon' => 'mountain-sun',
                'color' => '#16A34A',
                'educational_level' => 'basico',
                'typical_duration_hours' => 12,
                'requires_ordination' => false,
                'continuing_education_credit' => false,
                'target_ministries' => json_encode(['pastoral', 'diaconal', 'musical', 'jovens']),
                'open_to_laity' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'Seminário Bíblico',
                'slug' => 'seminario-biblico',
                'description' => 'Estudos exegéticos e hermenêuticos das Escrituras Sagradas',
                'icon' => 'book-bible',
                'color' => '#7C3AED',
                'educational_level' => 'intermediario',
                'typical_duration_hours' => 8,
                'requires_ordination' => false,
                'continuing_education_credit' => true,
                'target_ministries' => json_encode(['pastoral', 'diaconal']),
                'open_to_laity' => true,
                'display_order' => 5,
            ],
            [
                'name' => 'Workshop de Pregação',
                'slug' => 'workshop-pregacao',
                'description' => 'Técnicas práticas de homilética e comunicação da Palavra',
                'icon' => 'bullhorn',
                'color' => '#DC2626',
                'educational_level' => 'especializado',
                'typical_duration_hours' => 6,
                'requires_ordination' => false,
                'continuing_education_credit' => true,
                'target_ministries' => json_encode(['pastoral', 'diaconal', 'jovens']),
                'open_to_laity' => true,
                'display_order' => 6,
            ],
            [
                'name' => 'Formação Diaconal',
                'slug' => 'formacao-diaconal',
                'description' => 'Capacitação específica para o ministério diaconal e serviço cristão',
                'icon' => 'hands-praying',
                'color' => '#1D4ED8',
                'educational_level' => 'basico',
                'typical_duration_hours' => 24,
                'requires_ordination' => false,
                'continuing_education_credit' => true,
                'target_ministries' => json_encode(['diaconal']),
                'open_to_laity' => true,
                'display_order' => 7,
            ],
            [
                'name' => 'Assembleia Pastoral',
                'slug' => 'assembleia-pastoral',
                'description' => 'Encontro deliberativo e administrativo da liderança pastoral',
                'icon' => 'gavel',
                'color' => '#0EA5E9',
                'educational_level' => 'avancado',
                'typical_duration_hours' => 4,
                'requires_ordination' => true,
                'continuing_education_credit' => false,
                'target_ministries' => json_encode(['pastoral']),
                'open_to_laity' => false,
                'display_order' => 8,
            ],
        ];

        foreach ($types as $type) {
            $type['created_at'] = now();
            $type['updated_at'] = now();
            DB::table('event_types')->insert($type);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};