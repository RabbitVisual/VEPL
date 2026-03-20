<?php

namespace Modules\Sermons\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Sermons\App\Models\SermonSeries;

class SermonSeriesController extends Controller
{
    public function index(Request $request): View
    {
        $query = SermonSeries::withCount(['sermons', 'outlines']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        $series = $query->orderByDesc('created_at')->paginate(15);

        return view('sermons::admin.series.index', compact('series'));
    }

    public function create(): View
    {
        return view('sermons::admin.series.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);

        if ($request->hasFile('image_file')) {
            $validated['cover_image'] = $request->file('image_file')->store('sermons/series', 'public');
        }

        SermonSeries::create($validated);

        return redirect()->route('admin.sermons.series.index')->with('success', 'Série criada com sucesso!');
    }

    public function edit(SermonSeries $series): View
    {
        return view('sermons::admin.series.edit', compact('series'));
    }

    public function update(Request $request, SermonSeries $series): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);

        if ($request->string('title') !== $series->title) {
            $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);
        }

        if ($request->hasFile('image_file')) {
            if ($series->cover_image && \Storage::disk('public')->exists($series->cover_image)) {
                \Storage::disk('public')->delete($series->cover_image);
            }
            $validated['cover_image'] = $request->file('image_file')->store('sermons/series', 'public');
        }

        $series->update($validated);

        return redirect()->route('admin.sermons.series.index')->with('success', 'Série atualizada com sucesso!');
    }

    public function destroy(SermonSeries $series): RedirectResponse
    {
        $series->delete();

        return redirect()->route('admin.sermons.series.index')->with('success', 'Série removida com sucesso!');
    }
}

