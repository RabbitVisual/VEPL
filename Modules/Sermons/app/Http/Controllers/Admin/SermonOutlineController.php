<?php

namespace Modules\Sermons\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Sermons\App\Models\SermonCategory;
use Modules\Sermons\App\Models\SermonOutline;
use Modules\Sermons\App\Models\SermonSeries;

class SermonOutlineController extends Controller
{
    public function index(Request $request): View
    {
        $query = SermonOutline::with(['series', 'category', 'user']);

        if ($request->filled('sermon_series_id')) {
            $query->where('sermon_series_id', $request->integer('sermon_series_id'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('subtitle', 'like', "%{$search}%"));
        }

        $studies = $query->orderByDesc('created_at')->paginate(15);
        $series = SermonSeries::all();
        $categories = SermonCategory::active()->ordered()->get();

        return view('sermons::admin.studies.index', compact('studies', 'series', 'categories'));
    }

    public function create(): View
    {
        $series = SermonSeries::all();
        $categories = SermonCategory::active()->ordered()->get();

        return view('sermons::admin.studies.create', compact('series', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'sermon_series_id' => 'nullable|exists:sermon_series,id',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,members,private',
            'is_featured' => 'boolean',
            'video_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:40960',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }
        if ($request->hasFile('cover_image_file')) {
            $validated['cover_image'] = $request->file('cover_image_file')->store('sermons/studies', 'public');
        }
        if ($request->hasFile('audio_file')) {
            $validated['audio_url'] = $request->file('audio_file')->store('studies/audio', 'public');
        }
        unset($validated['audio_file']);

        SermonOutline::create($validated);

        return redirect()->route('admin.sermons.studies.index')->with('success', 'Esboço homilético criado com sucesso!');
    }

    public function edit(SermonOutline $study): View
    {
        $series = SermonSeries::all();
        $categories = SermonCategory::active()->ordered()->get();

        return view('sermons::admin.studies.edit', compact('study', 'series', 'categories'));
    }

    public function update(Request $request, SermonOutline $study): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'sermon_series_id' => 'nullable|exists:sermon_series,id',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,members,private',
            'is_featured' => 'boolean',
            'video_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:40960',
        ]);

        if ($request->string('title') !== $study->title) {
            $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);
        }
        if ($validated['status'] === 'published' && ! $study->published_at) {
            $validated['published_at'] = now();
        }
        if ($request->hasFile('cover_image_file')) {
            if ($study->cover_image && \Storage::disk('public')->exists($study->cover_image)) {
                \Storage::disk('public')->delete($study->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image_file')->store('sermons/studies', 'public');
        }
        if ($request->hasFile('audio_file')) {
            if ($study->audio_url && ! filter_var($study->audio_url, FILTER_VALIDATE_URL) && \Storage::disk('public')->exists($study->audio_url)) {
                \Storage::disk('public')->delete($study->audio_url);
            }
            $validated['audio_url'] = $request->file('audio_file')->store('studies/audio', 'public');
        }
        unset($validated['audio_file']);

        $study->update($validated);

        return redirect()->route('admin.sermons.studies.index')->with('success', 'Esboço homilético atualizado com sucesso!');
    }

    public function destroy(SermonOutline $study): RedirectResponse
    {
        if ($study->audio_url && ! filter_var($study->audio_url, FILTER_VALIDATE_URL) && \Storage::disk('public')->exists($study->audio_url)) {
            \Storage::disk('public')->delete($study->audio_url);
        }
        $study->delete();

        return redirect()->route('admin.sermons.studies.index')->with('success', 'Esboço removido com sucesso!');
    }
}

