<?php

declare(strict_types=1);

namespace Modules\NepeSearch\App\Services;

use Illuminate\Support\Collection;
use Modules\Academy\App\Models\Course;
use Modules\Academy\App\Models\Lesson;
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
                'academy_courses' => collect(),
                'academy_lessons' => collect(),
                'community_topics' => collect(),
            ];
        }

        return [
            'sermons' => $this->searchSermons($query),
            'bible' => $this->searchBible($query),
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
            ->get();
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
}
