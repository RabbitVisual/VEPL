<?php

namespace Modules\Sermons\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Sermons\App\Models\SermonSeries;

class SermonSeriesController extends Controller
{
    public function index(): View
    {
        $series = SermonSeries::where('status', 'published')
            ->withCount(['sermons', 'outlines'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('sermons::memberpanel.series.index', compact('series'));
    }

    public function show(SermonSeries $series): View
    {
        if ($series->status !== 'published') {
            abort(404);
        }

        $series->load([
            'sermons' => function ($q) {
                $q->published()->orderBy('created_at', 'desc');
            },
            'outlines' => function ($q) {
                $q->where('status', 'published')->orderBy('created_at', 'desc');
            },
        ]);

        return view('sermons::memberpanel.series.show', compact('series'));
    }
}

