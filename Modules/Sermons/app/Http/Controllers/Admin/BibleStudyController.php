<?php

namespace Modules\Sermons\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Sermons\App\Models\BibleSeries;
use Modules\Sermons\App\Models\BibleStudy;
use Modules\Sermons\App\Models\SermonCategory;

class BibleStudyController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibleStudy::with(['series', 'category', 'user']);

        if ($request->has('series_id') && ! empty($request->series_id)) {
            $query->where('series_id', $request->series_id);
        }

        if ($request->has('category_id') && ! empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        $studies = $query->orderBy('created_at', 'desc')->paginate(15);
        $series = BibleSeries::all();
        $categories = SermonCategory::active()->ordered()->get();

        return view('sermons::admin.studies.index', compact('studies', 'series', 'categories'));
    }

    public function create(): View
    {
        $series = BibleSeries::all();
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
            'series_id' => 'nullable|exists:bible_series,id',
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

        // Handle cover image upload
        if ($request->hasFile('cover_image_file')) {
            $path = $request->file('cover_image_file')->store('sermons/studies', 'public');
            $validated['cover_image'] = $path;
        }

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store('studies/audio', 'public');
            $validated['audio_url'] = $audioPath; // Store file path in audio_url
        }
        // Remove audio_file from validated data as it's not a database column
        unset($validated['audio_file']);

        BibleStudy::create($validated);

        return redirect()->route('admin.sermons.studies.index')
            ->with('success', 'Estudo Bíblico criado com sucesso!');
    }

    public function edit(BibleStudy $study): View
    {
        $series = BibleSeries::all();
        $categories = SermonCategory::active()->ordered()->get();

        return view('sermons::admin.studies.edit', compact('study', 'series', 'categories'));
    }

    public function update(Request $request, BibleStudy $study): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'series_id' => 'nullable|exists:bible_series,id',
            'category_id' => 'nullable|exists:sermon_categories,id',
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,members,private',
            'is_featured' => 'boolean',
            'video_url' => 'nullable|url',
            'audio_url' => 'nullable|url', // Keep audio_url validation for external URLs
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:40960', // Keep for file upload input
        ]);

        if ($request->has('title') && $request->title !== $study->title) {
            $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(6);
        }

        if ($validated['status'] === 'published' && ! $study->published_at) {
            $validated['published_at'] = now();
        }

        // Handle Cover Image Removal/Upload
        if ($request->input('remove_cover') == '1') {
            if ($study->cover_image && \Storage::disk('public')->exists($study->cover_image)) {
                \Storage::disk('public')->delete($study->cover_image);
            }
            $validated['cover_image'] = null;
        } elseif ($request->hasFile('cover_image_file')) {
            if ($study->cover_image && \Storage::disk('public')->exists($study->cover_image)) {
                \Storage::disk('public')->delete($study->cover_image);
            }
            $path = $request->file('cover_image_file')->store('sermons/studies', 'public');
            $validated['cover_image'] = $path;
        }

        // Handle Audio Removal
        if ($request->input('remove_audio') == '1') {
            // If audio_url points to a local file, delete it
            if ($study->audio_url && ! filter_var($study->audio_url, FILTER_VALIDATE_URL) && \Storage::disk('public')->exists($study->audio_url)) {
                \Storage::disk('public')->delete($study->audio_url);
            }
            $validated['audio_url'] = null;
        }
        // Handle audio file upload
        elseif ($request->hasFile('audio_file')) {
            // Delete old audio file if exists and is a local file
            if ($study->audio_url && ! filter_var($study->audio_url, FILTER_VALIDATE_URL) && \Storage::disk('public')->exists($study->audio_url)) {
                \Storage::disk('public')->delete($study->audio_url);
            }

            $audioPath = $request->file('audio_file')->store('studies/audio', 'public');
            $validated['audio_url'] = $audioPath; // Store new file path in audio_url
        }
        // If audio_url is provided in the request and it's not a file upload
        elseif (isset($validated['audio_url']) && ! empty($validated['audio_url'])) {
            // If the existing audio_url was a local file, delete it
            if ($study->audio_url && ! filter_var($study->audio_url, FILTER_VALIDATE_URL) && \Storage::disk('public')->exists($study->audio_url)) {
                \Storage::disk('public')->delete($study->audio_url);
            }
            // The new audio_url (external URL) is already in $validated
        } else {
            // If no audio_file uploaded and no audio_url provided, and there was a local file, delete it
            if ($study->audio_url && ! filter_var($study->audio_url, FILTER_VALIDATE_URL) && \Storage::disk('public')->exists($study->audio_url)) {
                \Storage::disk('public')->delete($study->audio_url);
            }
            $validated['audio_url'] = null;
        }

        // Remove audio_file from validated data as it's not a database column
        unset($validated['audio_file']);

        $study->update($validated);

        return redirect()->route('admin.sermons.studies.index')
            ->with('success', 'Estudo Bíblico atualizado com sucesso!');
    }

    public function destroy(BibleStudy $study): RedirectResponse
    {
        // Delete audio file if exists
        if ($study->audio_url && \Storage::disk('public')->exists($study->audio_url)) {
            \Storage::disk('public')->delete($study->audio_url);
        }

        $study->delete();

        return redirect()->route('admin.sermons.studies.index')
            ->with('success', 'Estudo removido com sucesso!');
    }
}
