<?php

declare(strict_types=1);

namespace Modules\NepeSearch\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\NepeSearch\App\Services\SearchEngineService;

class NepeSearchController extends Controller
{
    public function search(Request $request, SearchEngineService $searchEngine): JsonResponse
    {
        $term = (string) $request->string('q', '');
        return response()->json($searchEngine->search($term));
    }
}
