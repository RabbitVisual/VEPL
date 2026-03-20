<?php

namespace Modules\Sermons\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Sermons\App\Models\SermonCategory;
use Modules\Sermons\App\Models\SermonOutline;
use Modules\Sermons\App\Models\SermonSeries;

class SermonOutlineController extends Controller
{
    public function index(Request $request): View
    {
        $query = SermonOutline::where('status', 'published')
            ->with(['series', 'category']);

        if ($request->filled('sermon_series_id')) {
            $query->where('sermon_series_id', $request->integer('sermon_series_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        $studies = $query->orderBy('published_at', 'desc')->paginate(12);
        $series = SermonSeries::where('status', 'published')->get();
        $categories = SermonCategory::active()->get();

        return view('sermons::memberpanel.studies.index', compact('studies', 'series', 'categories'));
    }

    public function show(SermonOutline $study): View
    {
        if ($study->status !== 'published') {
            abort(404);
        }

        $study->load(['series', 'category', 'user']);
        $study->increment('views');

        return view('sermons::memberpanel.studies.show', compact('study'));
    }
}

