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
        if (Schema::hasTable('sermon_tags') && ! Schema::hasColumn('sermon_tags', 'deleted_at')) {
            Schema::table('sermon_tags', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (Schema::hasTable('sermon_comments') && ! Schema::hasColumn('sermon_comments', 'deleted_at')) {
            Schema::table('sermon_comments', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sermon_tags') && Schema::hasColumn('sermon_tags', 'deleted_at')) {
            Schema::table('sermon_tags', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('sermon_comments') && Schema::hasColumn('sermon_comments', 'deleted_at')) {
            Schema::table('sermon_comments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};

