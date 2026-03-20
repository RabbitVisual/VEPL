<?php

namespace Modules\Bible\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleBookPanorama;
use Modules\Bible\App\Models\BibleOfficialCommentary;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\StrongsCorrection;
use Modules\Bible\App\Models\StrongsLexicon;
use Modules\Bible\App\Services\BibleApiService;

class BibleContextApiController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    /**
     * GET /api/v1/bible/context?ref=João 3:16
     *
     * @return JsonResponse
     */
    public function context(Request $request): JsonResponse
    {
        $ref = $request->query('ref');
        if (! is_string($ref) || trim($ref) === '') {
            return response()->json(['message' => 'Parâmetro ref é obrigatório.'], 400);
        }

        $ref = trim(preg_replace('/\s+/u', ' ', $ref));

        $result = $this->bibleApi->findByReference($ref);
        if ($result === null) {
            return response()->json(['message' => 'Referência não encontrada ou formato inválido.'], 404);
        }

        $verses = $result['verses'];
        $verseIds = $verses->pluck('id')->all();

        $text = $verses
            ->map(fn ($v) => trim((string) $v->text))
            ->filter()
            ->implode(' ');

        $originalLanguage = $this->buildOriginalLanguagePayload($verseIds);

        $bookNumber = (int) ($result['book_number'] ?? 0);
        $firstVerse = $verses->first();
        $officialCommentary = null;

        if ($firstVerse !== null) {
            $chapterBookId = (int) Chapter::query()
                ->where('id', (int) $firstVerse->chapter_id)
                ->value('book_id');

            $commentary = BibleOfficialCommentary::query()
                ->where('book_id', $chapterBookId)
                ->where('chapter_id', (int) $firstVerse->chapter_id)
                ->where('verse_id', (int) $firstVerse->id)
                ->where('is_published', true)
                ->first();

            if ($commentary !== null) {
                $officialCommentary = $this->sanitizeOfficialCommentary((string) $commentary->official_commentary);
            }
        }

        $panorama = null;
        if ($bookNumber >= 1 && $bookNumber <= 66) {
            $p = BibleBookPanorama::query()
                ->where('book_number', $bookNumber)
                ->where('language', 'pt')
                ->first();
            if ($p) {
                $panorama = [
                    'author' => $p->author,
                    'date_written' => $p->date_written,
                    'theme_central' => $p->theme_central,
                    'recipients' => $p->recipients,
                    'testament' => $p->testament,
                ];
            }
        }

        return response()->json([
            'data' => [
                'reference' => $result['reference'],
                'book' => $result['book'] ?? null,
                'book_number' => $bookNumber ?: null,
                'chapter' => $result['chapter'] ?? null,
                'text' => $text,
                'original_language' => $originalLanguage,
                'panorama' => $panorama,
                'official_commentary' => $officialCommentary,
                'full_chapter_url' => $result['full_chapter_url'] ?? null,
            ],
        ]);
    }

    /**
     * @param  array<int, int>  $verseIds
     * @return array<int, array<string, mixed>>
     */
    private function buildOriginalLanguagePayload(array $verseIds): array
    {
        if ($verseIds === []) {
            return [];
        }

        $rows = DB::table('bible_word_tags')
            ->whereIn('verse_id', $verseIds)
            ->orderBy('verse_id')
            ->orderBy('position')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $strongNumbers = [];
        foreach ($rows as $row) {
            $n = $this->normalizeStrongNumber($row->strong_number ?? null);
            if ($n !== null) {
                $strongNumbers[] = $n;
            }
        }
        $strongNumbers = array_values(array_unique($strongNumbers));

        $lexiconByNumber = [];
        if ($strongNumbers !== []) {
            $lexiconByNumber = StrongsLexicon::query()
                ->whereIn('number', $strongNumbers)
                ->get()
                ->keyBy('number');
        }

        $correctionsByNumber = $this->latestApprovedCorrectionsByStrong($strongNumbers);

        $out = [];
        foreach ($rows as $row) {
            $norm = $this->normalizeStrongNumber($row->strong_number ?? null);
            $lex = $norm !== null ? ($lexiconByNumber[$norm] ?? null) : null;

            $lexiconPayload = null;
            if ($lex) {
                $lexiconPayload = [
                    'number' => $lex->number,
                    'lang' => $lex->lang,
                    'lemma' => $lex->lemma,
                    'pronounce' => $lex->pronounce,
                    'xlit' => $lex->xlit,
                    'description_pt' => $lex->description_pt,
                    'lemma_br' => $lex->lemma_br,
                ];
                $ov = $correctionsByNumber[$lex->number] ?? [];
                if ($ov !== []) {
                    $lexiconPayload['overrides_from_corrections'] = $ov;
                }
            }

            $out[] = [
                'verse_id' => (int) $row->verse_id,
                'position' => (int) $row->position,
                'word_surface' => $row->word_surface,
                'strong_number_raw' => $row->strong_number,
                'strong_number' => $norm,
                'morphology' => $row->morphology,
                'lang' => $row->lang,
                'lexicon' => $lexiconPayload,
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, string>  $strongNumbers
     * @return array<string, array<string, string>>
     */
    private function latestApprovedCorrectionsByStrong(array $strongNumbers): array
    {
        if ($strongNumbers === []) {
            return [];
        }

        $rows = StrongsCorrection::query()
            ->whereIn('strong_number', $strongNumbers)
            ->where('status', 'approved')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get(['strong_number', 'field', 'proposed_value']);

        $map = [];
        foreach ($rows as $row) {
            $key = $row->strong_number;
            if (! isset($map[$key])) {
                $map[$key] = [];
            }
            if (! isset($map[$key][$row->field])) {
                $map[$key][$row->field] = (string) $row->proposed_value;
            }
        }

        return $map;
    }

    private function normalizeStrongNumber(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $raw = trim($raw);
        if (preg_match('/^([HG]\d+)/iu', $raw, $m)) {
            return strtoupper($m[1]);
        }

        $first = preg_split('/[\/,;\s]+/u', $raw)[0] ?? $raw;
        if (preg_match('/^([HG]\d+)/iu', (string) $first, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }

    private function sanitizeOfficialCommentary(string $html): string
    {
        return trim(strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><blockquote><h2><h3><h4><a>'));
    }
}
