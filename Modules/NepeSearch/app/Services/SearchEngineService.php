<?php

declare(strict_types=1);

namespace Modules\NepeSearch\App\Services;

use Illuminate\Support\Collection;
use Modules\Academy\App\Models\Course;
use Modules\Academy\App\Models\Lesson;
use Modules\Bible\App\Models\BibleBookPanorama;
use Modules\Bible\App\Models\StrongsLexicon;
use Modules\Bible\App\Models\Verse;
use Modules\Community\App\Models\ForumTopic;
use Modules\Sermons\App\Models\Sermon;

class SearchEngineService
{
    public function search(string $term): array
    {
        $query = trim($term);
        if ($query === '') {
            return [
                'sermons' => collect(),
                'bible' => collect(),
                'bible_lexicon' => collect(),
                'bible_panoramas' => collect(),
                'academy_courses' => collect(),
                'academy_lessons' => collect(),
                'community_topics' => collect(),
            ];
        }

        return [
            'sermons' => $this->searchSermons($query),
            'bible' => $this->searchBible($query),
            'bible_lexicon' => $this->searchLexicon($query),
            'bible_panoramas' => $this->searchPanoramas($query),
            'academy_courses' => $this->searchCourses($query),
            'academy_lessons' => $this->searchLessons($query),
            'community_topics' => $this->searchTopics($query),
        ];
    }

    private function searchSermons(string $query): Collection
    {
        return Sermon::query()
            ->select(['id', 'title', 'description'])
            ->where(function ($builder) use ($query): void {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('full_content', 'like', "%{$query}%");
            })
            ->where('status', Sermon::STATUS_PUBLISHED)
            ->limit(6)
            ->get();
    }

    private function searchBible(string $query): Collection
    {
        return Verse::query()
            ->with('chapter.book')
            ->where('text', 'like', "%{$query}%")
            ->limit(6)
            ->get()
            ->map(function (Verse $verse) {
                return [
                    'id' => $verse->id,
                    'title' => $verse->full_reference,
                    'description' => (string) $verse->text,
                    'url' => route('memberpanel.bible.chapter', [
                        'version' => $verse->chapter->book->bible_version_id,
                        'book' => $verse->chapter->book_id,
                        'chapter' => $verse->chapter_id,
                    ]) . '#verse-' . $verse->id,
                ];
            });
    }

    private function searchCourses(string $query): Collection
    {
        return Course::query()
            ->select(['id', 'title', 'description', 'level'])
            ->where(function ($builder) use ($query): void {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(6)
            ->get();
    }

    private function searchLessons(string $query): Collection
    {
        return Lesson::query()
            ->with('module.course')
            ->select(['id', 'module_id', 'title', 'content_text'])
            ->where(function ($builder) use ($query): void {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('content_text', 'like', "%{$query}%");
            })
            ->limit(6)
            ->get();
    }

    private function searchTopics(string $query): Collection
    {
        return ForumTopic::query()
            ->with('category')
            ->select(['id', 'category_id', 'title', 'body'])
            ->where(function ($builder) use ($query): void {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('body', 'like', "%{$query}%");
            })
            ->latest()
            ->limit(6)
            ->get();
    }

    private function searchLexicon(string $query): Collection
    {
        return StrongsLexicon::query()
            ->select(['id', 'number', 'lemma', 'xlit', 'description_pt', 'lemma_br', 'lang'])
            ->where(function ($builder) use ($query): void {
                $builder->where('number', 'like', "%{$query}%")
                    ->orWhere('xlit', 'like', "%{$query}%")
                    ->orWhere('lemma', 'like', "%{$query}%")
                    ->orWhere('lemma_br', 'like', "%{$query}%")
                    ->orWhere('description_pt', 'like', "%{$query}%");
            })
            ->limit(6)
            ->get()
            ->map(function (StrongsLexicon $entry) {
                return [
                    'id' => $entry->id,
                    'title' => $entry->number . ' - ' . ($entry->lemma_br ?: $entry->lemma ?: 'Termo original'),
                    'description' => (string) ($entry->description_pt ?: $entry->xlit),
                    'url' => route('memberpanel.bible.strong.show', ['number' => $entry->number]),
                ];
            });
    }

    private function searchPanoramas(string $query): Collection
    {
        return BibleBookPanorama::query()
            ->select(['id', 'book_number', 'author', 'theme_central', 'recipients'])
            ->where('language', 'pt')
            ->where(function ($builder) use ($query): void {
                $builder->where('theme_central', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhere('recipients', 'like', "%{$query}%");
            })
            ->orderBy('book_number')
            ->limit(6)
            ->get()
            ->map(function (BibleBookPanorama $panorama) {
                return [
                    'id' => $panorama->id,
                    'title' => 'Panorama do Livro #' . $panorama->book_number,
                    'description' => (string) ($panorama->theme_central ?: $panorama->author ?: 'Contexto histórico e teológico'),
                    'url' => route('memberpanel.bible.read') . '?book_number=' . $panorama->book_number . '&tab=panorama',
                ];
            });
    }
}
