<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Templates de Crachás para Formações VEPL.
     * 
     * Sistema de geração de crachás personalizados com informações
     * ministeriais, função eclesiástica e dados específicos para
     * identificação em formações pastorais.
     */
    public function up(): void
    {
        Schema::create('event_badges', function (Blueprint $table) {
            // ── Identificação e Vinculação ────────────────────────────────────────
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade')->comment('Formação vinculada');
            
            // ── Template de Design ────────────────────────────────────────────────
            $table->string('template_name')->comment('Nome do template');
            $table->longText('template_html')->nullable()->comment('Template HTML do crachá');
            $table->longText('template_css')->nullable()->comment('Estilos CSS específicos');
            $table->enum('design_theme', ['classic', 'modern', 'ministerial', 'academic', 'corporate'])->default('ministerial')->comment('Tema visual');
            
            // ── Configurações de Impressão ────────────────────────────────────────
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait')->comment('Orientação do crachá');
            $table->enum('paper_size', ['A4', 'Letter', '10x7cm', '9x5cm', 'custom'])->default('A4')->comment('Tamanho do papel');
            $table->integer('badges_per_page')->default(8)->comment('Crachás por página');
            $table->json('print_margins')->nullable()->comment('Margens de impressão');
            $table->integer('print_dpi')->default(300)->comment('DPI para impressão');

            // ── Campos e Informações Exibidas ─────────────────────────────────────
            $table->json('visible_fields')->nullable()->comment('Campos visíveis no crachá');
            $table->json('field_positions')->nullable()->comment('Posições dos campos no layout');
            $table->json('font_configurations')->nullable()->comment('Configurações de fontes');
            $table->boolean('show_photo')->default(true)->comment('Exibe foto do participante');
            $table->boolean('show_qr_code')->default(true)->comment('Exibe QR code');
            $table->boolean('show_ministry_title')->default(true)->comment('Exibe título ministerial');
            $table->boolean('show_church_affiliation')->default(true)->comment('Exibe igreja de afiliação');

            // ── Elementos Visuais ─────────────────────────────────────────────────
            $table->string('logo_position', 20)->default('top-left')->comment('Posição do logo');
            $table->string('background_image')->nullable()->comment('Imagem de fundo');
            $table->string('background_color', 20)->default('#ffffff')->comment('Cor de fundo');
            $table->json('color_scheme')->nullable()->comment('Esquema de cores');
            $table->boolean('use_gradient')->default(false)->comment('Usar gradiente');

            // ── Segurança e Autenticidade ─────────────────────────────────────────
            $table->boolean('include_security_features')->default(true)->comment('Inclui recursos de segurança');
            $table->string('security_code_format')->nullable()->comment('Formato do código de segurança');
            $table->boolean('watermark_enabled')->default(false)->comment('Marca d\'água habilitada');
            $table->string('watermark_text')->nullable()->comment('Texto da marca d\'água');

            // ── Personalização por Segmento ───────────────────────────────────────
            $table->json('segment_customizations')->nullable()->comment('Personalizações por segmento');
            $table->boolean('different_colors_by_role')->default(false)->comment('Cores diferentes por função');
            $table->json('role_color_mapping')->nullable()->comment('Mapeamento de cores por função');

            // ── Metadados e Configurações ─────────────────────────────────────────
            $table->json('template_variables')->nullable()->comment('Variáveis disponíveis no template');
            $table->boolean('is_active')->default(true)->comment('Template ativo');
            $table->boolean('is_default')->default(false)->comment('Template padrão');
            $table->string('version', 10)->default('1.0')->comment('Versão do template');

            $table->timestamps();

            // ── Índices ────────────────────────────────────────────────────────────
            $table->index(['event_id', 'is_active'], 'ebg_event_active_idx');
            $table->index('design_theme', 'ebg_theme_idx');
            $table->index(['is_default', 'event_id'], 'ebg_default_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_badges');
    }
};