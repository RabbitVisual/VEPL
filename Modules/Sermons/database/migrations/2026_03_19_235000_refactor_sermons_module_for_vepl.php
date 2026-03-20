<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameLegacyTables();
        $this->refactorSermonsTable();
        $this->refactorBibleReferencesTable();
        $this->refactorOutlineTable();
    }

    public function down(): void
    {
        if (Schema::hasTable('sermon_bible_references')) {
            Schema::table('sermon_bible_references', function (Blueprint $table): void {
                if (Schema::hasColumn('sermon_bible_references', 'verse_start_id')) {
                    $table->dropForeign(['verse_start_id']);
                    $table->dropColumn('verse_start_id');
                }
                if (Schema::hasColumn('sermon_bible_references', 'verse_end_id')) {
                    $table->dropForeign(['verse_end_id']);
                    $table->dropColumn('verse_end_id');
                }
            });
        }

        if (Schema::hasTable('sermons') && Schema::hasColumn('sermons', 'sermon_series_id')) {
            Schema::table('sermons', function (Blueprint $table): void {
                $table->dropForeign(['sermon_series_id']);
            });

            Schema::table('sermons', function (Blueprint $table): void {
                $table->renameColumn('sermon_series_id', 'series_id');
            });
        }

        if (Schema::hasTable('sermon_outlines') && Schema::hasColumn('sermon_outlines', 'sermon_series_id')) {
            Schema::table('sermon_outlines', function (Blueprint $table): void {
                $table->dropForeign(['sermon_series_id']);
            });

            Schema::table('sermon_outlines', function (Blueprint $table): void {
                $table->renameColumn('sermon_series_id', 'series_id');
            });
        }

        if (Schema::hasTable('sermon_series') && ! Schema::hasTable('bible_series')) {
            Schema::rename('sermon_series', 'bible_series');
        }

        if (Schema::hasTable('sermon_outlines') && ! Schema::hasTable('bible_studies')) {
            Schema::rename('sermon_outlines', 'bible_studies');
        }

        if (Schema::hasTable('sermon_exegesis') && ! Schema::hasTable('bible_commentaries')) {
            Schema::rename('sermon_exegesis', 'bible_commentaries');
        }
    }

    private function renameLegacyTables(): void
    {
        if (Schema::hasTable('bible_series') && ! Schema::hasTable('sermon_series')) {
            Schema::rename('bible_series', 'sermon_series');
        }

        if (Schema::hasTable('bible_studies') && ! Schema::hasTable('sermon_outlines')) {
            Schema::rename('bible_studies', 'sermon_outlines');
        }

        if (Schema::hasTable('bible_commentaries') && ! Schema::hasTable('sermon_exegesis')) {
            Schema::rename('bible_commentaries', 'sermon_exegesis');
        }
    }

    private function refactorSermonsTable(): void
    {
        if (! Schema::hasTable('sermons')) {
            return;
        }

        if (Schema::hasColumn('sermons', 'series_id') && ! Schema::hasColumn('sermons', 'sermon_series_id')) {
            try {
                Schema::table('sermons', function (Blueprint $table): void {
                    $table->dropForeign(['series_id']);
                });
            } catch (\Throwable) {
                // Ignore if FK name differs in previous schema state.
            }

            Schema::table('sermons', function (Blueprint $table): void {
                $table->renameColumn('series_id', 'sermon_series_id');
            });
        }

        Schema::table('sermons', function (Blueprint $table): void {
            if (! Schema::hasColumn('sermons', 'theme')) {
                $table->string('theme')->nullable()->after('title');
            }
            if (! Schema::hasColumn('sermons', 'sermon_series_id')) {
                $table->foreignId('sermon_series_id')->nullable()->after('category_id');
            }
            if (! Schema::hasColumn('sermons', 'biblical_text_base')) {
                $table->string('biblical_text_base')->nullable()->after('description');
            }
            if (! Schema::hasColumn('sermons', 'historical_context')) {
                $table->text('historical_context')->nullable()->after('biblical_text_base');
            }
            if (! Schema::hasColumn('sermons', 'central_proposition')) {
                $table->text('central_proposition')->nullable()->after('historical_context');
            }
            if (! Schema::hasColumn('sermons', 'body_outline')) {
                $table->longText('body_outline')->nullable()->after('introduction');
            }
            if (! Schema::hasColumn('sermons', 'practical_application')) {
                $table->text('practical_application')->nullable()->after('body_outline');
            }
        });

        if (Schema::hasColumn('sermons', 'development') && Schema::hasColumn('sermons', 'body_outline')) {
            DB::table('sermons')
                ->whereNull('body_outline')
                ->whereNotNull('development')
                ->update(['body_outline' => DB::raw('development')]);
        }

        if (Schema::hasColumn('sermons', 'application') && Schema::hasColumn('sermons', 'practical_application')) {
            DB::table('sermons')
                ->whereNull('practical_application')
                ->whereNotNull('application')
                ->update(['practical_application' => DB::raw('application')]);
        }

        if (Schema::hasColumn('sermons', 'sermon_series_id')) {
            Schema::table('sermons', function (Blueprint $table): void {
                $table->foreign('sermon_series_id')->references('id')->on('sermon_series')->nullOnDelete();
            });
        }
    }

    private function refactorBibleReferencesTable(): void
    {
        if (! Schema::hasTable('sermon_bible_references')) {
            return;
        }

        Schema::table('sermon_bible_references', function (Blueprint $table): void {
            if (! Schema::hasColumn('sermon_bible_references', 'verse_start_id')) {
                $table->foreignId('verse_start_id')->nullable()->after('chapter_id');
            }
            if (! Schema::hasColumn('sermon_bible_references', 'verse_end_id')) {
                $table->foreignId('verse_end_id')->nullable()->after('verse_start_id');
            }
        });

        Schema::table('sermon_bible_references', function (Blueprint $table): void {
            if (Schema::hasColumn('sermon_bible_references', 'verse_start_id')) {
                $table->foreign('verse_start_id')->references('id')->on('verses')->nullOnDelete();
            }
            if (Schema::hasColumn('sermon_bible_references', 'verse_end_id')) {
                $table->foreign('verse_end_id')->references('id')->on('verses')->nullOnDelete();
            }
        });
    }

    private function refactorOutlineTable(): void
    {
        if (! Schema::hasTable('sermon_outlines')) {
            return;
        }

        if (Schema::hasColumn('sermon_outlines', 'series_id') && ! Schema::hasColumn('sermon_outlines', 'sermon_series_id')) {
            try {
                Schema::table('sermon_outlines', function (Blueprint $table): void {
                    $table->dropForeign(['series_id']);
                });
            } catch (\Throwable) {
                // Ignore if FK name differs in previous schema state.
            }

            Schema::table('sermon_outlines', function (Blueprint $table): void {
                $table->renameColumn('series_id', 'sermon_series_id');
            });
        }

        if (Schema::hasColumn('sermon_outlines', 'sermon_series_id')) {
            Schema::table('sermon_outlines', function (Blueprint $table): void {
                $table->foreign('sermon_series_id')->references('id')->on('sermon_series')->nullOnDelete();
            });
        }
    }
};

