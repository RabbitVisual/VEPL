<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sermon_comments', function (Blueprint $table) {
            // Renomear content para comment para alinhar com o modelo
            if (Schema::hasColumn('sermon_comments', 'content')) {
                $table->renameColumn('content', 'comment');
            }

            // Adicionar colunas faltantes
            if (!Schema::hasColumn('sermon_comments', 'type')) {
                $table->string('type')->default('comment')->after('comment');
            }

            if (!Schema::hasColumn('sermon_comments', 'status')) {
                $table->string('status')->default('approved')->after('type');
            }

            if (!Schema::hasColumn('sermon_comments', 'likes')) {
                $table->integer('likes')->default(0)->after('status');
            }

            if (!Schema::hasColumn('sermon_comments', 'reference_section')) {
                $table->string('reference_section')->nullable()->after('likes');
            }

            if (!Schema::hasColumn('sermon_comments', 'reference_text')) {
                $table->text('reference_text')->nullable()->after('reference_section');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sermon_comments', function (Blueprint $table) {
            if (Schema::hasColumn('sermon_comments', 'comment')) {
                $table->renameColumn('comment', 'content');
            }

            $table->dropColumn(['type', 'status', 'likes', 'reference_section', 'reference_text']);
        });
    }
};
