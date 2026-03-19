<?php

declare(strict_types=1);

namespace Modules\Community\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Community\App\Models\ForumTopic;

class CommunityFeedController extends Controller
{
    public function index(): View
    {
        $topics = ForumTopic::query()
            ->with(['category', 'user', 'replies'])
            ->latest()
            ->paginate(12);

        return view('community::memberpanel.index', compact('topics'));
    }
}
