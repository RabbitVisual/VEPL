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
        Schema::table('bible_word_tags', function (Blueprint $table) {
            $table->string('strong_number', 64)->nullable()->change();
            $table->string('morphology', 64)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bible_word_tags', function (Blueprint $table) {
            $table->string('strong_number', 12)->nullable()->change();
            $table->string('morphology', 32)->nullable()->change();
        });
    }
};
