<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;

class BibleController extends Controller
{
    public function index()
    {
        $versions = BibleVersion::withCount('books')->orderBy('is_default', 'desc')->orderBy('name')->get();

        return view('bible::admin.bible.index', compact('versions'));
    }

    public function create()
    {
        return redirect()->route('admin.bible.import');
    }

    public function show($bible)
    {
        $version = BibleVersion::findOrFail($bible);

        $books = Book::where('bible_version_id', $version->id)
            ->ordered()
            ->withCount(['chapters', 'verses'])
            ->get();

        $oldTestament = $books->where('testament', 'old');
        $newTestament = $books->where('testament', 'new');

        return view('bible::admin.bible.show', compact('version', 'books', 'oldTestament', 'newTestament'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10|unique:bible_versions,abbreviation',
            'description' => 'nullable|string',
            'language' => 'required|string|max:10',
            'file' => 'required|file|mimes:json|max:10240',
            'is_default' => 'boolean',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        $privatePath = storage_path('app/private/bible/offline');
        if (! is_dir($privatePath)) {
            mkdir($privatePath, 0755, true);
        }

        $file->move($privatePath, $fileName);
        $fullPath = $privatePath.'/'.$fileName;

        $command = "bible:import-json \"{$fullPath}\" --name=\"{$validated['name']}\" --abbreviation=\"{$validated['abbreviation']}\"";

        if ($request->has('is_default')) {
            $command .= ' --default';
        }

        Artisan::call($command);

        return redirect()->route('admin.bible.index')
            ->with('success', 'Versão da Bíblia importada com sucesso!');
    }

    public function edit($bible)
    {
        $version = BibleVersion::findOrFail($bible);

        return view('bible::admin.bible.edit', compact('version'));
    }

    public function update(Request $request, $bible)
    {
        $version = BibleVersion::findOrFail($bible);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:10|unique:bible_versions,abbreviation,'.$version->id,
            'description' => 'nullable|string',
            'language' => 'required|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($request->has('is_default') && $request->is_default) {
            BibleVersion::where('id', '!=', $version->id)->update(['is_default' => false]);
        }

        $version->update($validated);

        return redirect()->route('admin.bible.index')
            ->with('success', 'Versão atualizada com sucesso!');
    }

    public function destroy($bible)
    {
        $version = BibleVersion::findOrFail($bible);
        $version->delete();

        return redirect()->route('admin.bible.index')
            ->with('success', 'Versão excluída com sucesso!');
    }

    public function importForm()
    {
        $jsonPath = storage_path('app/private/bible/offline');
        $availableFiles = [];

        if (is_dir($jsonPath)) {
            $files = glob($jsonPath.'/*.json');
            foreach ($files as $file) {
                $basename = basename($file);
                if ($basename !== 'index.json') {
                    $availableFiles[] = $basename;
                }
            }
        }

        $indexPath = storage_path('app/private/bible/offline/index.json');
        $versionsInfo = [];
        if (file_exists($indexPath)) {
            $indexContent = file_get_contents($indexPath);
            $indexData = json_decode($indexContent, true);
            if (isset($indexData['versions'])) {
                $versionsInfo = $indexData['versions'];
            }
        }

        return view('bible::admin.bible.import', compact('availableFiles', 'versionsInfo'));
    }

    public function viewBook($version, $book)
    {
        $versionModel = BibleVersion::findOrFail($version);
        $bookModel = Book::findOrFail($book);

        if ($bookModel->bible_version_id != $versionModel->id) {
            abort(404, 'Livro não encontrado nesta versão da Bíblia');
        }

        $chapters = $bookModel->chapters()->withCount('verses')->orderBy('chapter_number')->get();

        return view('bible::admin.bible.book', [
            'version' => $versionModel,
            'book' => $bookModel,
            'chapters' => $chapters,
        ]);
    }

    public function viewChapter($version, $book, $chapter)
    {
        $versionModel = BibleVersion::findOrFail($version);
        $bookModel = Book::findOrFail($book);
        $chapterModel = Chapter::findOrFail($chapter);

        if ($chapterModel->book_id != $bookModel->id || $bookModel->bible_version_id != $versionModel->id) {
            abort(404, 'Capítulo não encontrado nesta versão da Bíblia');
        }

        $verses = Verse::where('chapter_id', $chapterModel->id)
            ->orderBy('verse_number', 'asc')
            ->get();

        $previousChapter = Chapter::where('book_id', $bookModel->id)
            ->where('chapter_number', '<', $chapterModel->chapter_number)
            ->orderBy('chapter_number', 'desc')
            ->first();

        $nextChapter = Chapter::where('book_id', $bookModel->id)
            ->where('chapter_number', '>', $chapterModel->chapter_number)
            ->orderBy('chapter_number', 'asc')
            ->first();

        if (! $nextChapter) {
            $nextBook = Book::where('bible_version_id', $versionModel->id)
                ->where('book_number', '>', $bookModel->book_number)
                ->orderBy('book_number', 'asc')
                ->first();
            if ($nextBook) {
                $nextChapter = $nextBook->chapters()->orderBy('chapter_number', 'asc')->first();
            }
        }

        if (! $previousChapter) {
            $previousBook = Book::where('bible_version_id', $versionModel->id)
                ->where('book_number', '<', $bookModel->book_number)
                ->orderBy('book_number', 'desc')
                ->first();
            if ($previousBook) {
                $previousChapter = $previousBook->chapters()->orderBy('chapter_number', 'desc')->first();
            }
        }

        return view('bible::admin.bible.chapter', [
            'version' => $versionModel,
            'book' => $bookModel,
            'chapter' => $chapterModel,
            'verses' => $verses,
            'previousChapter' => $previousChapter,
            'nextChapter' => $nextChapter,
        ]);
    }
}
