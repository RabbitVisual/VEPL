<?php

namespace Modules\Bible\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class ImportGreekTagged extends Command
{
    protected $signature   = 'bible:import-greek-tagged {--chunk=2000 : Registros por batch de upsert}';
    protected $description = 'Importa as tags gregas (GRC-Κοινη/trparsed.json) para bible_word_tags';

    protected array $bookNameToNumber = [
        // NT canônico (40-66)
        'Matthew' => 40, 'Mark' => 41, 'Luke' => 42, 'John' => 43, 'Acts' => 44,
        'Romans' => 45, '1 Corinthians' => 46, '2 Corinthians' => 47,
        'Galatians' => 48, 'Ephesians' => 49, 'Philippians' => 50, 'Colossians' => 51,
        '1 Thessalonians' => 52, '2 Thessalonians' => 53,
        '1 Timothy' => 54, '2 Timothy' => 55, 'Titus' => 56, 'Philemon' => 57,
        'Hebrews' => 58, 'James' => 59,
        '1 Peter' => 60, '2 Peter' => 61,
        '1 John' => 62, '2 John' => 63, '3 John' => 64,
        'Jude' => 65, 'Revelation' => 66,
    ];

    public function handle(): int
    {
        $path = storage_path('app/private/bible/offline/GRC-Κοινη/trparsed.json');

        if (! File::exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            $this->line('Verifique se o arquivo trparsed.json existe dentro do diretório GRC-Κοινη.');
            return self::FAILURE;
        }

        $this->info('📖 Carregando GRC-Κοινη/trparsed.json…');
        $data = json_decode(File::get($path), true);

        if (empty($data)) {
            $this->error('JSON vazio ou inválido.');
            return self::FAILURE;
        }

        $verses = $data['verses'] ?? $data;
        $this->info('Total de entradas: ' . count($verses));

        // Limpar registros gregos anteriores
        $this->warn('🗑  Removendo tags gregas anteriores…');
        DB::table('bible_word_tags')->where('lang', 'gr')->delete();

        $chunk  = (int) $this->option('chunk');
        $bar    = $this->output->createProgressBar(count($verses));
        $bar->start();

        // Pré-carregar índice de verse_ids para o NT
        $this->info('');
        $this->info('🔍 Construindo índice de versículos do NT…');
        $verseIndex = $this->buildVerseIndex();

        $rows       = [];
        $totalWords = 0;

        foreach ($verses as $vEntry) {
            $bookName = $vEntry['book_name'] ?? ($vEntry['book'] ?? null);
            $chapter  = (int) ($vEntry['chapter'] ?? 0);
            $verse    = (int) ($vEntry['verse']   ?? 0);
            $text     = $vEntry['text'] ?? '';

            $bookNumber = $this->bookNameToNumber[$bookName] ?? null;

            if (! $bookNumber || ! $chapter || ! $verse) {
                $bar->advance();
                continue;
            }

            $verseId = $verseIndex[$bookNumber][$chapter][$verse] ?? null;

            if (! $verseId) {
                $bar->advance();
                continue;
            }

            // Parsear palavras: cada segmento "palavra G1234 morfologia"
            preg_match_all('/(\S+)\s+(G\d+)\s+(\S+)/u', $text, $matches, PREG_SET_ORDER);

            foreach ($matches as $pos => $m) {
                $rows[] = [
                    'verse_id'      => $verseId,
                    'position'      => $pos,
                    'word_surface'  => $m[1],
                    'strong_number' => $m[2],
                    'morphology'    => $m[3],
                    'lang'          => 'gr',
                ];
                $totalWords++;

                if (count($rows) >= $chunk) {
                    DB::table('bible_word_tags')->upsert(
                        $rows,
                        ['verse_id', 'position', 'lang'],
                        ['word_surface', 'strong_number', 'morphology']
                    );
                    $rows = [];
                }
            }

            $bar->advance();
        }

        if (! empty($rows)) {
            DB::table('bible_word_tags')->upsert(
                $rows,
                ['verse_id', 'position', 'lang'],
                ['word_surface', 'strong_number', 'morphology']
            );
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$totalWords} palavras gregas importadas para bible_word_tags.");

        return self::SUCCESS;
    }

    protected function buildVerseIndex(): array
    {
        $index = [];

        $bookIds = Book::whereIn('book_number', range(40, 66))
            ->pluck('id', 'book_number');

        foreach ($bookIds as $bookNumber => $bookId) {
            $chapters = Chapter::where('book_id', $bookId)->get();
            foreach ($chapters as $chapter) {
                $verses = Verse::where('chapter_id', $chapter->id)
                    ->select(['id', 'verse_number'])
                    ->get();
                foreach ($verses as $verse) {
                    $index[$bookNumber][$chapter->chapter_number][$verse->verse_number] = $verse->id;
                }
            }
        }

        return $index;
    }
}
