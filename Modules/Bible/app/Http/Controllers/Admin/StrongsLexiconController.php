<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bible\App\Models\StrongsLexicon;

class StrongsLexiconController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $lexicons = StrongsLexicon::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('number', 'like', "%{$q}%")
                    ->orWhere('lemma', 'like', "%{$q}%")
                    ->orWhere('lemma_br', 'like', "%{$q}%")
                    ->orWhere('description_pt', 'like', "%{$q}%");
            })
            ->orderBy('lang')
            ->orderBy('number')
            ->paginate(25)
            ->withQueryString();

        return view('bible::admin.strongs.lexicon-index', [
            'lexicons' => $lexicons,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('bible::admin.strongs.lexicon-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|string|max:10|unique:bible_strongs_lexicon,number',
            'lang' => 'required|in:he,gr',
            'lemma' => 'nullable|string|max:255',
            'pronounce' => 'nullable|string|max:100',
            'xlit' => 'nullable|string|max:200',
            'description_pt' => 'nullable|string',
            'lemma_br' => 'nullable|string',
        ]);

        $data['number'] = strtoupper(trim((string) $data['number']));

        StrongsLexicon::create($data + [
            'is_reviewed' => false,
        ]);

        return redirect()->route('admin.bible.strongs-lexicon.index')->with('success', 'Entrada Strong criada com sucesso.');
    }

    public function show($lexicon)
    {
        $entry = StrongsLexicon::findOrFail($lexicon);

        return view('bible::admin.strongs.lexicon-show', [
            'entry' => $entry,
        ]);
    }

    public function edit($lexicon)
    {
        $entry = StrongsLexicon::findOrFail($lexicon);

        return view('bible::admin.strongs.lexicon-edit', [
            'entry' => $entry,
        ]);
    }

    public function update(Request $request, $lexicon)
    {
        $entry = StrongsLexicon::findOrFail($lexicon);

        $data = $request->validate([
            'lang' => 'required|in:he,gr',
            'lemma' => 'nullable|string|max:255',
            'pronounce' => 'nullable|string|max:100',
            'xlit' => 'nullable|string|max:200',
            'description_pt' => 'nullable|string',
            'lemma_br' => 'nullable|string',
            'is_reviewed' => 'sometimes|boolean',
        ]);

        $wasReviewed = (bool) $entry->is_reviewed;
        $nowReviewed = (bool) ($request->boolean('is_reviewed'));

        $entry->update([
            'lang' => $data['lang'],
            'lemma' => $data['lemma'] ?? null,
            'pronounce' => $data['pronounce'] ?? null,
            'xlit' => $data['xlit'] ?? null,
            'description_pt' => $data['description_pt'] ?? null,
            'lemma_br' => $data['lemma_br'] ?? null,
            'is_reviewed' => $nowReviewed,
        ]);

        if (! $wasReviewed && $nowReviewed) {
            $entry->update([
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        }

        return redirect()->route('admin.bible.strongs-lexicon.edit', $entry->id)->with('success', 'Entrada Strong atualizada com sucesso.');
    }

    public function destroy($lexicon)
    {
        $entry = StrongsLexicon::findOrFail($lexicon);
        $entry->delete();

        return redirect()->route('admin.bible.strongs-lexicon.index')->with('info', 'Entrada Strong removida.');
    }
}

