<?php

namespace Modules\Bible\App\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Bible\App\Models\Book;

/**
 * Detecta referências bíblicas em texto livre e substitui por botões interativos.
 *
 * Padrões de livro são carregados da versão padrão (nomes + abreviações) para
 * evitar falsos positivos e manter aderência ao catálogo importado.
 */
class BibleReferenceParserService
{
    private const CACHE_KEY = 'bible_reference_book_pattern_v1';

    private const CACHE_TTL_SECONDS = 3600;

    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    /**
     * Converte referências encontradas em HTML com botões `.bible-reference-link`.
     *
     * Segmenta por tags HTML para não alterar atributos ou markup existente.
     */
    public function parseText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $parts = preg_split('/(<[^>]+>)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            return $text;
        }

        $out = '';
        foreach ($parts as $i => $part) {
            if ($part === '') {
                continue;
            }
            if (($i % 2) === 1) {
                $out .= $part;

                continue;
            }
            $out .= $this->parsePlainSegment($part);
        }

        return $out;
    }

    private function parsePlainSegment(string $segment): string
    {
        $pattern = $this->getBookAlternationPattern();
        if ($pattern === null || $pattern === '') {
            return $segment;
        }

        $regex = '/(?<![\p{L}\p{N}])(?:@)?(?P<book>'.$pattern.')\s+(?P<ch>\d+):(?P<v1>\d+)(?:-(?P<v2>\d+))?\b/u';

        return (string) preg_replace_callback(
            $regex,
            function (array $m): string {
                $book = trim($m['book']);
                $chapter = $m['ch'];
                $v1 = $m['v1'];
                $v2 = $m['v2'] ?? '';
                $label = $m[0];
                if (str_starts_with($label, '@')) {
                    $label = ltrim(substr($label, 1));
                }
                $ref = $book.' '.$chapter.':'.$v1;
                if ($v2 !== '') {
                    $ref .= '-'.$v2;
                }

                return '<button type="button" class="bible-reference-link text-amber-600 hover:underline font-semibold cursor-pointer transition-colors" data-reference="'
                    .e($ref).'">'.e($label).'</button>';
            },
            $segment
        );
    }

    /**
     * Alternância regex (mais longa primeiro) para nomes/abreviações de livros da versão padrão.
     */
    private function getBookAlternationPattern(): ?string
    {
        $versionId = $this->bibleApi->getDefaultVersionId();
        if (! $versionId) {
            return null;
        }

        return Cache::remember(self::CACHE_KEY.'_'.$versionId, self::CACHE_TTL_SECONDS, function () use ($versionId) {
            $aliases = Book::query()
                ->where('bible_version_id', $versionId)
                ->get(['name', 'abbreviation'])
                ->flatMap(fn (Book $b) => array_filter([$b->name, $b->abbreviation]))
                ->unique()
                ->values()
                ->all();

            if ($aliases === []) {
                return '';
            }

            usort($aliases, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

            $escaped = array_map(fn (string $s) => preg_quote($s, '/'), $aliases);

            return implode('|', $escaped);
        });
    }

    /**
     * Invalida cache do padrão (ex.: após import de nova versão padrão).
     */
    public function clearBookPatternCache(): void
    {
        $versionId = $this->bibleApi->getDefaultVersionId();
        if ($versionId) {
            Cache::forget(self::CACHE_KEY.'_'.$versionId);
        }
    }
}
