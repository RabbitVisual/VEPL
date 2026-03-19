<?php

declare(strict_types=1);

namespace Modules\Academy\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Academy\App\Models\Course;
use Modules\Academy\App\Models\Lesson;
use Modules\Academy\App\Models\StudentProgress;

class ClassroomController extends Controller
{
    public function show(Course $course, ?Lesson $lesson = null): View
    {
        $course->load([
            'modules.lessons.attachments',
            'modules.lessons.progress' => fn ($query) => $query->where('user_id', Auth::id()),
        ]);

        $currentLesson = $lesson;
        if (! $currentLesson) {
            $currentLesson = $course->modules->flatMap->lessons->first();
        }

        $completedLessonIds = StudentProgress::query()
            ->where('user_id', Auth::id())
            ->whereHas('lesson.module', fn ($query) => $query->where('course_id', $course->id))
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->all();

        return view('academy::memberpanel.classroom', [
            'course' => $course,
            'currentLesson' => $currentLesson,
            'completedLessonIds' => $completedLessonIds,
        ]);
    }
}
