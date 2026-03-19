<?php

namespace Modules\Bible\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class ImportHebrewTagged extends Command
{
    protected $signature   = 'bible:import-hebrew-tagged {--chunk=2000 : Registros por batch de insert}';
    protected $description = 'Importa as tags hebraicas (hebrew_tagged.json) para bible_word_tags';

    /**
     * Map between book name in JSON (English) → book_number canônico (1-39 AT)
     */
    protected array $bookNameToNumber = [
        'Genesis' => 1, 'Exodus' => 2, 'Leviticus' => 3, 'Numbers' => 4, 'Deuteronomy' => 5,
        'Joshua' => 6, 'Judges' => 7, 'Ruth' => 8,
        'I Samuel' => 9, 'II Samuel' => 10,
        'I Kings' => 11, 'II Kings' => 12,
        'I Chronicles' => 13, 'II Chronicles' => 14,
        'Ezra' => 15, 'Nehemiah' => 16, 'Esther' => 17, 'Job' => 18, 'Psalms' => 19,
        'Proverbs' => 20, 'Ecclesiastes' => 21, 'Song of Solomon' => 22, 'Isaiah' => 23,
        'Jeremiah' => 24, 'Lamentations' => 25, 'Ezekiel' => 26, 'Daniel' => 27,
        'Hosea' => 28, 'Joel' => 29, 'Amos' => 30, 'Obadiah' => 31, 'Jonah' => 32,
        'Micah' => 33, 'Nahum' => 34, 'Habakkuk' => 35, 'Zephaniah' => 36,
        'Haggai' => 37, 'Zechariah' => 38, 'Malachi' => 39,
    ];

    public function handle(): int
    {
        $path = storage_path('app/private/bible/offline/hebrew_tagged.json');

        if (! File::exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return self::FAILURE;
        }

        $this->info('📖 Carregando hebrew_tagged.json (pode demorar ~10s)…');
        $tagged = json_decode(File::get($path), true);

        if (empty($tagged)) {
            $this->error('JSON vazio ou inválido.');
            return self::FAILURE;
        }

        // Limpar registros hebraicos anteriores
        $this->warn('🗑  Removendo tags hebraicas anteriores…');
        DB::table('bible_word_tags')->where('lang', 'he')->delete();

        $chunk  = (int) $this->option('chunk');
        $books  = count($tagged);
        $bar    = $this->output->createProgressBar($books);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        $rows        = [];
        $totalWords  = 0;

        // Pré-carregar mapa verse_id: book_number → chapter → verse_number
        $this->info('');
        $this->info('🔍 Construindo índice de versículos…');

        // Cache: book_number → chapter_number → verse_number => verse_id
        $verseIndex = $this->buildVerseIndex();

        foreach ($tagged as $bookName => $chapters) {
            $bookNumber = $this->bookNameToNumber[$bookName] ?? null;
            $bar->setMessage($bookName);

            if (! $bookNumber) {
                $this->line("  ⚠️  Livro não mapeado: {$bookName}");
                $bar->advance();
                continue;
            }

            foreach ($chapters as $chIdx => $verses) {
                $chapterNumber = $chIdx + 1;

                foreach ($verses as $vIdx => $words) {
                    $verseNumber = $vIdx + 1;
                    $verseId = $verseIndex[$bookNumber][$chapterNumber][$verseNumber] ?? null;

                    if (! $verseId) {
                        continue; // Versículo não importado no banco ainda
                    }

                    foreach ($words as $pos => $word) {
                        // word = [surface, strong, morphology]
                        $rows[] = [
                            'verse_id'     => $verseId,
                            'position'     => $pos,
                            'word_surface' => $word[0] ?? '',
                            'strong_number'=> $word[1] ?? null,
                            'morphology'   => $word[2] ?? null,
                            'lang'         => 'he',
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
        $this->info("✅ {$totalWords} palavras hebraicas importadas para bible_word_tags.");

        return self::SUCCESS;
    }

    protected function buildVerseIndex(): array
    {
        $index = [];

        // Pegar apenas versículos do AT (livros 1-39)
        $bookIds = Book::whereIn('book_number', range(1, 39))
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
