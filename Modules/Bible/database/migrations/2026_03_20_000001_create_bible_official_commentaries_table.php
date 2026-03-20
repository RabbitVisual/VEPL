<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bible_official_commentaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignId('verse_id')->constrained('verses')->cascadeOnDelete();
            $table->longText('official_commentary');
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('verse_id');
            $table->index(['book_id', 'chapter_id']);
            $table->index(['is_published', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bible_official_commentaries');
    }
};
