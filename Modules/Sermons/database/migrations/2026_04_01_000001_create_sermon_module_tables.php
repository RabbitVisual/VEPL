<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categorias de Sermões e Estudos
        Schema::create('sermon_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('sermon_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 2. Séries de Mensagens (Ex: "O Sermão do Monte", "Gálatas")
        Schema::create('bible_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Autor da série
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // 3. Sermões (A Tabela Principal de Homilética)
        Schema::create('sermons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Autor
            $table->foreignId('category_id')->nullable()->constrained('sermon_categories')->nullOnDelete();
            $table->foreignId('series_id')->nullable()->constrained('bible_series')->nullOnDelete();
            $table->foreignId('worship_suggestion_id')->nullable()->constrained('worship_songs')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('subtitle')->nullable();
            $table->text('description')->nullable(); // Resumo homilético

            // Estrutura do Esboço
            $table->longText('introduction')->nullable();
            $table->longText('development')->nullable(); // Tópicos/Pontos do Sermão
            $table->longText('conclusion')->nullable();
            $table->longText('application')->nullable();
            $table->longText('full_content')->nullable(); // Manuscrito completo

            // Tipo de Estrutura Científica/Homilética
            $table->string('sermon_structure_type', 50)->nullable(); // expositivo, temático, textual
            $table->json('structure_meta')->nullable(); // Metadados da estrutura (ex: divisões)

            // Mídia e Anexos
            $table->string('cover_image')->nullable();
            $table->json('attachments')->nullable(); // PDF, PPT, etc
            $table->string('audio_url')->nullable();
            $table->string('video_url')->nullable();

            // Status e visibilidade
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'members', 'private'])->default('members');
            $table->boolean('is_collaborative')->default(false);
            $table->boolean('is_featured')->default(false);

            // Estatísticas e Datas
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('downloads')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sermon_date')->nullable(); // Data em que foi pregado

            // Histórico
            $table->integer('version')->default(1);
            $table->foreignId('parent_id')->nullable()->constrained('sermons')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index(['user_id', 'status']);

            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'subtitle', 'description', 'full_content']);
            }
        });

        // 4. Tags Temáticas
        Schema::create('sermon_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('sermon_tag_pivot', function (Blueprint $table) {
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('sermon_tags')->cascadeOnDelete();
            $table->primary(['sermon_id', 'tag_id']);
        });

        // 5. Referências Bíblicas (com suporte a Exegese)
        Schema::create('sermon_bible_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();

            // Referência
            $table->string('book'); // Nome do Livro
            $table->integer('chapter')->nullable();
            $table->string('verses')->nullable();
            $table->text('reference_text')->nullable(); // Texto bíblico citado

            // Links com o módulo Bible
            $table->foreignId('bible_version_id')->nullable()->constrained('bible_versions')->nullOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();

            // Notas de Exegese e Contexto
            $table->enum('type', ['main', 'support', 'illustration', 'other'])->default('main');
            $table->text('context')->nullable();
            $table->text('exegesis_notes')->nullable(); // Notas de estudo original (grego/hebraico/gramática)
            $table->integer('order')->default(0);

            $table->timestamps();
            $table->index(['sermon_id', 'type', 'order']);
        });

        // 6. Estudos Bíblicos (Independentes de Sermões)
        Schema::create('bible_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('series_id')->nullable()->constrained('bible_series')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('sermon_categories')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('subtitle')->nullable();
            $table->text('description')->nullable();

            $table->longText('content'); // Conteúdo do estudo
            $table->string('cover_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable(); // Alias para audio_file anterior

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'members', 'private'])->default('members');
            $table->boolean('is_featured')->default(false);

            $table->integer('views')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'visibility', 'series_id']);
        });

        // 7. Comentários Exegéticos (Doutrinário/Exegético por versículo)
        Schema::create('bible_commentaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->nullOnDelete();

            $table->string('book'); // Fallback string
            $table->integer('chapter');
            $table->integer('verse_start');
            $table->integer('verse_end')->nullable();

            $table->string('title')->nullable();
            $table->longText('content'); // O comentário em si

            $table->string('audio_url')->nullable();
            $table->string('cover_image')->nullable();

            $table->enum('status', ['draft', 'published'])->default('published');
            $table->boolean('is_official')->default(false); // Comentário oficial da igreja/doutrina

            $table->timestamps();
            $table->softDeletes();

            $table->index(['book', 'chapter', 'verse_start']);
            $table->index('user_id');
        });

        // 8. Colaboradores de Sermões (Co-autoria pastoral)
        Schema::create('sermon_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 20)->default('editor'); // editor, viewer
            $table->boolean('can_edit')->default(true);
            $table->string('status', 20)->default('pending'); // pending, accepted, declined
            $table->timestamp('invited_at')->nullable();
            $table->timestamps();

            $table->unique(['sermon_id', 'user_id']);
        });

        // 9. Comentários Sociais em Sermões/Estudos
        Schema::create('sermon_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('sermon_comments')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        // 10. Favoritos/Salvos
        Schema::create('sermon_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sermon_id')->constrained('sermons')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'sermon_id']);
        });

        // 11. Notas de Estudo Pessoais (Cross-reference)
        Schema::create('sermon_study_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sermon_id')->nullable()->constrained('sermons')->nullOnDelete();
            $table->string('reference_type', 50)->nullable(); // bible_study, bible_commentary, verse
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sermon_study_notes');
        Schema::dropIfExists('sermon_favorites');
        Schema::dropIfExists('sermon_comments');
        Schema::dropIfExists('sermon_collaborators');
        Schema::dropIfExists('bible_commentaries');
        Schema::dropIfExists('bible_studies');
        Schema::dropIfExists('sermon_bible_references');
        Schema::dropIfExists('sermon_tag_pivot');
        Schema::dropIfExists('sermon_tags');
        Schema::dropIfExists('sermons');
        Schema::dropIfExists('bible_series');
        Schema::dropIfExists('sermon_categories');
    }
};
