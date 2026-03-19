<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabelas de conteúdo bíblico — Módulo Bible (Core).
     *
     * Conteúdo: bible_versions → books → chapters → verses
     * Estudo:   bible_metadata, bible_favorites, bible_book_panoramas
     * Interlinear: bible_strongs_lexicon, bible_word_tags,
     *              bible_strongs_corrections, bible_interlinear_notes
     *
     * NOTA: Funcionalidade de áudio (bible_chapter_audio / audio_url_template)
     *       foi deliberadamente REMOVIDA do projeto.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // VERSÕES DA BÍBLIA
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                              // "Almeida Revista e Atualizada"
            $table->string('abbreviation')->unique();           // "ARA", "ARC", "NVI"
            $table->string('description')->nullable();
            $table->string('language')->default('pt-BR');
            $table->string('file_name')->nullable();            // Nome do CSV importado
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('total_books')->default(66);
            $table->integer('total_chapters')->default(0);
            $table->integer('total_verses')->default(0);
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // LIVROS, CAPÍTULOS E VERSÍCULOS
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_version_id')->constrained('bible_versions')->cascadeOnDelete();
            $table->string('name');                            // "Gênesis"
            $table->integer('book_number');                   // 1–66
            $table->string('abbreviation')->nullable();       // "Gn"
            $table->enum('testament', ['old', 'new'])->default('old');
            $table->integer('total_chapters')->default(0);
            $table->integer('total_verses')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['bible_version_id', 'book_number']);
            $table->index(['bible_version_id', 'testament']);
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->integer('chapter_number');
            $table->integer('total_verses')->default(0);
            $table->timestamps();

            $table->unique(['book_id', 'chapter_number']);
            $table->index('book_id');
        });

        Schema::create('verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->integer('verse_number');
            $table->text('text');
            $table->bigInteger('original_verse_id')->nullable(); // ID original do CSV
            $table->timestamps();

            $table->unique(['chapter_id', 'verse_number']);
            $table->index('chapter_id');

            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText('text');                     // Busca full-text
            }
        });

        // ─────────────────────────────────────────────────────────────────────
        // METADADOS — contagem de versículos por capítulo/livro
        // (usado para geração automática de planos balanceados)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bible_version_id')->constrained('bible_versions')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->integer('chapter_number');
            $table->integer('verse_count')->default(0);
            $table->timestamps();

            $table->unique(['bible_version_id', 'book_id', 'chapter_number']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // FAVORITOS DO USUÁRIO
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verse_id')->constrained('verses')->cascadeOnDelete();
            $table->string('color', 20)->nullable();           // Cor de destaque (#ffee00)
            $table->timestamps();

            $table->unique(['user_id', 'verse_id']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // PANORAMA POR LIVRO
        // (Autor, data, tema central, destinatários — para o leitor e estudo)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_book_panoramas', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('book_number')->comment('1-66 canônico');
            $table->enum('testament', ['old', 'new'])->default('old');
            $table->string('author')->nullable();
            $table->string('date_written')->nullable();
            $table->text('theme_central')->nullable();
            $table->text('recipients')->nullable();
            $table->string('language', 10)->default('pt');
            $table->timestamps();

            $table->unique(['book_number', 'language']);
            $table->index('book_number');
        });

        // ═════════════════════════════════════════════════════════════════════
        // SISTEMA INTERLINEAR — NÍVEL NEPE BRASIL
        // ═════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────────────────────
        // DICIONÁRIO STRONG'S (H1–G5624)
        // Importado de storage/app/private/bible/offline/strongs.json
        // Tradução PT-BR: BSRTB 1.0 por Reinan Rodrigues / Vertex Solution
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_strongs_lexicon', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10)->unique();            // H1, H430, G2316…
            $table->enum('lang', ['he', 'gr']);                // Hebraico ou Grego
            $table->text('lemma')->nullable();                 // אָב / λόγος
            $table->string('pronounce', 100)->nullable();      // "awb" / "log'-os"
            $table->string('xlit', 200)->nullable();           // Transliteração acadêmica
            $table->text('description_pt')->nullable();        // Definição completa PT-BR
            $table->text('lemma_br')->nullable();              // Equivalente semântico PT (pode ser longo)
            $table->boolean('is_reviewed')->default(false);   // Revisado por teólogo?
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('lang');
            $table->index(['lang', 'number']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // PALAVRAS TAGUEADAS POR VERSÍCULO (Hebraico AT + Grego NT)
        // Posição de cada palavra na frase, com Strong e morfologia
        // Hebraico: hebrew_tagged.json | Grego: GRC-Κοινη/trparsed.json
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_word_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verse_id')->constrained('verses')->cascadeOnDelete();
            $table->unsignedSmallInteger('position');          // Ordem da palavra no versículo
            $table->string('word_surface');                    // Texto original (hebraico/grego)
            $table->string('strong_number', 12)->nullable();   // H430, G2316, Hc/H559…
            $table->string('morphology', 32)->nullable();      // HVqp3ms, N-NSM…
            $table->enum('lang', ['he', 'gr']);
            // Sem timestamps para evitar overhead em 700k+ registros

            $table->index(['verse_id', 'lang']);
            $table->index('strong_number');
            // Unique por versículo + posição + idioma
            $table->unique(['verse_id', 'position', 'lang'], 'word_tags_verse_pos_lang_unique');
        });

        // ─────────────────────────────────────────────────────────────────────
        // CORREÇÕES TEOLÓGICAS
        // Pastor/Admin identifica erro → submete → Admin aprova → DB atualiza
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_strongs_corrections', function (Blueprint $table) {
            $table->id();
            $table->string('strong_number', 10)->index();      // ex: H430
            $table->enum('field', ['description_pt', 'lemma_br', 'xlit', 'pronounce']); // campo corrigido
            $table->text('current_value');                     // Valor atual no banco
            $table->text('proposed_value');                    // Valor proposto
            $table->text('justification');                     // Argumentação teológica/linguística
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();           // Notas do revisor ao aprovar/rejeitar
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────────────
        // ANOTAÇÕES INTERLINEAR (por versículo e/ou Strong específico)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('bible_interlinear_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verse_id')->constrained('verses')->cascadeOnDelete();
            $table->string('strong_number', 10)->nullable()->index(); // Anotação sobre palavra específica
            $table->text('note');
            $table->boolean('is_public')->default(false);     // true = visível a todos os membros
            $table->timestamps();

            $table->index(['verse_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_interlinear_notes');
        Schema::dropIfExists('bible_strongs_corrections');
        Schema::dropIfExists('bible_word_tags');
        Schema::dropIfExists('bible_strongs_lexicon');
        Schema::dropIfExists('bible_book_panoramas');
        Schema::dropIfExists('bible_favorites');
        Schema::dropIfExists('bible_metadata');
        Schema::dropIfExists('verses');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('books');
        Schema::dropIfExists('bible_versions');
    }
};
