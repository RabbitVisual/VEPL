<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Bible\App\Models\BibleWordTag;

class BibleWordTagsController extends Controller
{
    public function index(Request $request)
    {
        $verseId = $request->query('verse_id');
        $strong = $request->query('strong_number');
        $lang = $request->query('lang');

        $query = BibleWordTag::query();

        $query->when($verseId !== null && $verseId !== '', fn ($q) => $q->where('verse_id', (int) $verseId));
        $query->when($strong !== null && $strong !== '', fn ($q) => $q->where('strong_number', $strong));
        $query->when($lang !== null && $lang !== '', fn ($q) => $q->where('lang', $lang));

        $wordTags = $query
            ->orderBy('verse_id')
            ->orderBy('position')
            ->paginate(30)
            ->withQueryString();

        return view('bible::admin.bible.word-tags-index', [
            'wordTags' => $wordTags,
            'verseId' => $verseId,
            'strong' => $strong,
            'lang' => $lang,
        ]);
    }

    public function create()
    {
        return view('bible::admin.bible.word-tags-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'verse_id' => 'required|integer',
            'position' => 'required|integer|min:0',
            'word_surface' => 'required|string|max:255',
            'strong_number' => 'nullable|string|max:12',
            'morphology' => 'nullable|string|max:32',
            'lang' => 'required|in:he,gr',
        ]);

        $data['position'] = (int) $data['position'];

        BibleWordTag::create($data);

        return redirect()->route('admin.bible.word-tags.index')->with('success', 'Word tag criado com sucesso.');
    }

    public function show($wordTag)
    {
        $entry = BibleWordTag::findOrFail($wordTag);
        return view('bible::admin.bible.word-tags-show', ['entry' => $entry]);
    }

    public function edit($wordTag)
    {
        $entry = BibleWordTag::findOrFail($wordTag);
        return view('bible::admin.bible.word-tags-edit', ['entry' => $entry]);
    }

    public function update(Request $request, $wordTag)
    {
        $entry = BibleWordTag::findOrFail($wordTag);

        $data = $request->validate([
            'verse_id' => 'required|integer',
            'position' => 'required|integer|min:0',
            'word_surface' => 'required|string|max:255',
            'strong_number' => 'nullable|string|max:12',
            'morphology' => 'nullable|string|max:32',
            'lang' => 'required|in:he,gr',
        ]);

        $entry->update($data);

        return redirect()->route('admin.bible.word-tags.edit', $entry->id)->with('success', 'Word tag atualizada com sucesso.');
    }

    public function destroy($wordTag)
    {
        $entry = BibleWordTag::findOrFail($wordTag);
        $entry->delete();

        return redirect()->route('admin.bible.word-tags.index')->with('info', 'Word tag removida.');
    }
}

