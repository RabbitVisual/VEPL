<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bible\App\Models\BibleOfficialCommentary;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class BibleMetadataController extends Controller
{
    public function index(Request $request)
    {
        $versionId = (int) $request->query('bible_version_id', 0);
        $bookId = (int) $request->query('book_id', 0);
        $chapterId = (int) $request->query('chapter_id', 0);
        $verseId = (int) $request->query('verse_id', 0);
        $status = $request->query('status', '');

        $items = BibleOfficialCommentary::query()
            ->with(['book:id,name,book_number,bible_version_id', 'book.bibleVersion:id,abbreviation', 'chapter:id,chapter_number', 'verse:id,verse_number'])
            ->when($versionId > 0, fn ($query) => $query->whereHas('book', fn ($book) => $book->where('bible_version_id', $versionId)))
            ->when($bookId > 0, fn ($query) => $query->where('book_id', $bookId))
            ->when($chapterId > 0, fn ($query) => $query->where('chapter_id', $chapterId))
            ->when($verseId > 0, fn ($query) => $query->where('verse_id', $verseId))
            ->when($status !== '', fn ($query) => $query->where('is_published', $status === 'published'))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $versions = BibleVersion::query()
            ->orderBy('abbreviation')
            ->get(['id', 'abbreviation', 'name']);

        $books = Book::query()
            ->when($versionId > 0, fn ($query) => $query->where('bible_version_id', $versionId))
            ->orderBy('book_number')
            ->get(['id', 'name', 'book_number', 'bible_version_id']);

        $chapters = collect();
        if ($bookId > 0) {
            $chapters = Chapter::query()
                ->where('book_id', $bookId)
                ->orderBy('chapter_number')
                ->get(['id', 'chapter_number']);
        }

        $verses = collect();
        if ($chapterId > 0) {
            $verses = Verse::query()
                ->where('chapter_id', $chapterId)
                ->orderBy('verse_number')
                ->get(['id', 'verse_number']);
        }

        return view('bible::admin.metadata.index', compact(
            'items',
            'versions',
            'books',
            'chapters',
            'verses',
            'versionId',
            'bookId',
            'chapterId',
            'verseId',
            'status'
        ));
    }

    public function create(Request $request)
    {
        return view('bible::admin.metadata.create', $this->buildFormData($request));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $this->assertHierarchy($data['bible_version_id'], $data['book_id'], $data['chapter_id'], $data['verse_id']);

        BibleOfficialCommentary::updateOrCreate(
            ['verse_id' => $data['verse_id']],
            collect($data)->except('bible_version_id')->toArray() + [
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        return redirect()->route('admin.bible.metadata.index')
            ->with('success', 'Comentário oficial salvo com sucesso.');
    }

    public function edit(BibleOfficialCommentary $metadatum, Request $request)
    {
        return view('bible::admin.metadata.edit', $this->buildFormData($request, $metadatum));
    }

    public function update(Request $request, BibleOfficialCommentary $metadatum)
    {
        $data = $this->validatePayload($request);
        $this->assertHierarchy($data['bible_version_id'], $data['book_id'], $data['chapter_id'], $data['verse_id']);

        $metadatum->update(collect($data)->except('bible_version_id')->toArray() + ['updated_by' => Auth::id()]);

        return redirect()->route('admin.bible.metadata.edit', $metadatum)
            ->with('success', 'Comentário oficial atualizado com sucesso.');
    }

    private function buildFormData(Request $request, ?BibleOfficialCommentary $item = null): array
    {
        $selectedVersionId = (int) old('bible_version_id', (int) $request->query('bible_version_id', $item?->book?->bible_version_id ?? 0));
        $selectedBookId = (int) old('book_id', (int) $request->query('book_id', $item?->book_id ?? 0));
        $selectedChapterId = (int) old('chapter_id', (int) $request->query('chapter_id', $item?->chapter_id ?? 0));
        $selectedVerseId = (int) old('verse_id', (int) $request->query('verse_id', $item?->verse_id ?? 0));

        $versions = BibleVersion::query()->orderBy('abbreviation')->get(['id', 'abbreviation', 'name']);
        $books = Book::query()
            ->when($selectedVersionId > 0, fn ($query) => $query->where('bible_version_id', $selectedVersionId))
            ->orderBy('book_number')
            ->get(['id', 'name', 'book_number', 'bible_version_id']);
        $chapters = $selectedBookId > 0
            ? Chapter::query()->where('book_id', $selectedBookId)->orderBy('chapter_number')->get(['id', 'chapter_number'])
            : collect();
        $verses = $selectedChapterId > 0
            ? Verse::query()->where('chapter_id', $selectedChapterId)->orderBy('verse_number')->get(['id', 'verse_number'])
            : collect();

        return compact('versions', 'books', 'chapters', 'verses', 'selectedVersionId', 'selectedBookId', 'selectedChapterId', 'selectedVerseId', 'item');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'bible_version_id' => 'required|integer|exists:bible_versions,id',
            'book_id' => 'required|integer|exists:books,id',
            'chapter_id' => 'required|integer|exists:chapters,id',
            'verse_id' => 'required|integer|exists:verses,id',
            'official_commentary' => 'required|string|min:10',
            'is_published' => 'sometimes|boolean',
        ]) + [
            'is_published' => $request->boolean('is_published'),
        ];
    }

    private function assertHierarchy(int $versionId, int $bookId, int $chapterId, int $verseId): void
    {
        $bookBelongs = Book::query()->where('id', $bookId)->where('bible_version_id', $versionId)->exists();
        $chapterBelongs = Chapter::query()->where('id', $chapterId)->where('book_id', $bookId)->exists();
        $verseBelongs = Verse::query()->where('id', $verseId)->where('chapter_id', $chapterId)->exists();

        abort_unless($bookBelongs && $chapterBelongs && $verseBelongs, 422, 'A referência bíblica selecionada é inválida.');
    }

    public function booksByVersion(Request $request)
    {
        $versionId = (int) $request->query('bible_version_id', 0);
        if ($versionId <= 0) {
            return response()->json(['data' => []]);
        }

        $books = Book::query()
            ->where('bible_version_id', $versionId)
            ->orderBy('book_number')
            ->get(['id', 'name', 'book_number']);

        return response()->json(['data' => $books]);
    }

    public function chaptersByBook(Request $request)
    {
        $bookId = (int) $request->query('book_id', 0);
        if ($bookId <= 0) {
            return response()->json(['data' => []]);
        }

        $chapters = Chapter::query()
            ->where('book_id', $bookId)
            ->orderBy('chapter_number')
            ->get(['id', 'chapter_number']);

        return response()->json(['data' => $chapters]);
    }

    public function versesByChapter(Request $request)
    {
        $chapterId = (int) $request->query('chapter_id', 0);
        if ($chapterId <= 0) {
            return response()->json(['data' => []]);
        }

        $verses = Verse::query()
            ->where('chapter_id', $chapterId)
            ->orderBy('verse_number')
            ->get(['id', 'verse_number']);

        return response()->json(['data' => $verses]);
    }
}
