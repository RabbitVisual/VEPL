<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Bible\App\Models\BibleBookPanorama;

class BibleBookPanoramaAdminController extends Controller
{
    public function index(Request $request)
    {
        $qBook = $request->query('book_number');
        $testament = $request->query('testament');

        $panoramas = BibleBookPanorama::query()
            ->when($qBook !== null && $qBook !== '', fn ($query) => $query->where('book_number', (int) $qBook))
            ->when($testament !== null && $testament !== '', fn ($query) => $query->where('testament', $testament))
            ->orderBy('book_number')
            ->paginate(25)
            ->withQueryString();

        return view('bible::admin.bible.panoramas-index', [
            'panoramas' => $panoramas,
            'qBook' => $qBook,
            'testament' => $testament,
        ]);
    }

    public function create()
    {
        return view('bible::admin.bible.panoramas-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'book_number' => 'required|integer|min:1|max:66',
            'testament' => 'required|in:old,new',
            'author' => 'nullable|string|max:255',
            'date_written' => 'nullable|string|max:255',
            'theme_central' => 'nullable|string',
            'recipients' => 'nullable|string',
            'language' => 'required|string|max:10',
        ]);

        BibleBookPanorama::create($data);

        return redirect()->route('admin.bible.panoramas.index')->with('success', 'Panorama criado com sucesso.');
    }

    public function show($panorama)
    {
        $entry = BibleBookPanorama::findOrFail($panorama);
        return view('bible::admin.bible.panoramas-show', ['entry' => $entry]);
    }

    public function edit($panorama)
    {
        $entry = BibleBookPanorama::findOrFail($panorama);
        return view('bible::admin.bible.panoramas-edit', ['entry' => $entry]);
    }

    public function update(Request $request, $panorama)
    {
        $entry = BibleBookPanorama::findOrFail($panorama);

        $data = $request->validate([
            'book_number' => 'required|integer|min:1|max:66',
            'testament' => 'required|in:old,new',
            'author' => 'nullable|string|max:255',
            'date_written' => 'nullable|string|max:255',
            'theme_central' => 'nullable|string',
            'recipients' => 'nullable|string',
            'language' => 'required|string|max:10',
        ]);

        $entry->update($data);

        return redirect()->route('admin.bible.panoramas.edit', $entry->id)->with('success', 'Panorama atualizado com sucesso.');
    }

    public function destroy($panorama)
    {
        $entry = BibleBookPanorama::findOrFail($panorama);
        $entry->delete();

        return redirect()->route('admin.bible.panoramas.index')->with('info', 'Panorama removido.');
    }
}

