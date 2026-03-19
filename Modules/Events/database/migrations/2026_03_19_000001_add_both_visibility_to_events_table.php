<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Avoid enum ALTER on SQLite (tests) and other non-MySQL engines.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // Ensure `both` exists as a visibility option for events.
        DB::statement("
            ALTER TABLE events
            MODIFY visibility ENUM('public','members','both','ministers_only')
            NOT NULL DEFAULT 'public'
        ");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // Revert to previous enum definition (without `both`).
        DB::statement("
            ALTER TABLE events
            MODIFY visibility ENUM('public','members','ministers_only')
            NOT NULL DEFAULT 'public'
        ");
    }
};

