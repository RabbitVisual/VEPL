<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sermons', function (Blueprint $table): void {
            if (! Schema::hasColumn('sermons', 'biblical_text_base')) {
                $table->string('biblical_text_base')->nullable()->after('description');
            }
            if (! Schema::hasColumn('sermons', 'central_proposition')) {
                $table->text('central_proposition')->nullable()->after('biblical_text_base');
            }
            if (! Schema::hasColumn('sermons', 'historical_context')) {
                $table->text('historical_context')->nullable()->after('central_proposition');
            }
            if (! Schema::hasColumn('sermons', 'exegesis_notes')) {
                $table->longText('exegesis_notes')->nullable()->after('historical_context');
            }
            if (! Schema::hasColumn('sermons', 'practical_application')) {
                $table->longText('practical_application')->nullable()->after('exegesis_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sermons', function (Blueprint $table): void {
            $columns = [
                'biblical_text_base',
                'central_proposition',
                'historical_context',
                'exegesis_notes',
                'practical_application',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('sermons', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
