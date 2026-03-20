<?php

namespace Modules\Sermons\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Bible\App\Services\BibleApiService;
use Modules\Sermons\App\Models\SermonExegesis;

class SermonExegesisController extends Controller
{
    public function __construct(
        private BibleApiService $bibleApi
    ) {}

    public function index(Request $request): View
    {
        $query = SermonExegesis::where('status', 'published')
            ->with('user');

        if ($request->filled('book')) {
            $query->where('book', (string) $request->input('book'));
        }

        if ($request->filled('chapter')) {
            $query->where('chapter', $request->integer('chapter'));
        }

        if ($request->filled('verse_number')) {
            $verseNum = $request->integer('verse_number');
            $query->where('verse_start', '<=', $verseNum)->where('verse_end', '>=', $verseNum);
        }

        $commentaries = $query->orderBy('book')
            ->orderBy('chapter')
            ->orderBy('verse_start')
            ->paginate(20);

        $books = SermonExegesis::where('status', 'published')
            ->select('book')
            ->distinct()
            ->orderBy('book')
            ->pluck('book');

        $bibleVersions = $this->bibleApi->getVersions();

        return view('sermons::memberpanel.commentaries.index', compact('commentaries', 'books', 'bibleVersions'));
    }

    public function show(SermonExegesis $commentary): View
    {
        if ($commentary->status !== 'published') {
            abort(404);
        }

        return view('sermons::memberpanel.commentaries.show', compact('commentary'));
    }
}

