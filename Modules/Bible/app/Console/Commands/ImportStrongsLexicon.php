<?php

namespace Modules\Bible\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportStrongsLexicon extends Command
{
    protected $signature = 'bible:import-strongs {--chunk=500 : Registros por batch de upsert}';

    protected $description = 'Importa o Dicionário Strong\'s (H+G) para a tabela bible_strongs_lexicon';

    public function handle(): int
    {
        $path = storage_path('app/private/bible/offline/strongs.json');

        if (! File::exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");

            return self::FAILURE;
        }

        $this->info('📖 Carregando strongs.json…');
        $raw = json_decode(File::get($path), true);
        $items = $raw['itens'] ?? $raw;

        if (empty($items)) {
            $this->error('Nenhum item encontrado no JSON.');

            return self::FAILURE;
        }

        $chunk = (int) $this->option('chunk');
        $total = count($items);
        $this->info("Total de entradas: {$total}");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $now = now();
        $rows = [];

        foreach ($items as $item) {
            $number = strtoupper(trim($item['number'] ?? ''));
            if (! $number) {
                $bar->advance();

                continue;
            }

            // Detectar idioma: H = hebraico, G = grego
            $lang = str_starts_with($number, 'H') ? 'he' : 'gr';

            $rows[] = [
                'number' => $number,
                'lang' => $lang,
                'lemma' => $item['lemma'] ?? null,
                'pronounce' => $item['pronounce'] ?? null,
                'xlit' => $item['xlit'] ?? null,
                'description_pt' => $item['description'] ?? null,
                'lemma_br' => $item['lemma_br'] ?? null,
                'is_reviewed' => false,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= $chunk) {
                DB::table('bible_strongs_lexicon')->upsert($rows, ['number'], [
                    'lang', 'lemma', 'pronounce', 'xlit', 'description_pt', 'lemma_br', 'updated_at',
                ]);
                $bar->advance(count($rows));
                $rows = [];
            }
        }

        // Flush restantes
        if (! empty($rows)) {
            DB::table('bible_strongs_lexicon')->upsert($rows, ['number'], [
                'lang', 'lemma', 'pronounce', 'xlit', 'description_pt', 'lemma_br', 'updated_at',
            ]);
            $bar->advance(count($rows));
        }

        $bar->finish();
        $this->newLine();

        $imported = DB::table('bible_strongs_lexicon')->count();
        $this->info("✅ Strong's importado com sucesso! Total na tabela: {$imported} entradas.");
        $this->line('   H (hebraico): '.DB::table('bible_strongs_lexicon')->where('lang', 'he')->count());
        $this->line('   G (grego):    '.DB::table('bible_strongs_lexicon')->where('lang', 'gr')->count());

        return self::SUCCESS;
    }
}
