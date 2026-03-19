<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;

// Core Models (Using aliases if Model not found to avoid crash, but using direct paths where known)

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // --- 1. Modules Overview ---
        $modules = Module::all();
        $modulesData = [];

        foreach ($modules as $module) {
            $modulesData[] = [
                'name' => $module->getName(),
                'alias' => $module->get('alias', $module->getLowerName()),
                'enabled' => $module->isEnabled(),
                'priority' => $module->get('priority', 0),
                'description' => $module->get('description', ''),
            ];
        }

        // --- 2. Core Statistics ---
        $stats = [
            'total_users' => DB::table('users')->count(),
            'active_users' => DB::table('users')->where('is_active', true)->count(),
            'total_modules' => count($modulesData),
            'enabled_modules' => count(array_filter($modulesData, fn ($m) => $m['enabled'])),
        ];

        // --- 3. Module Specific Stats (Active only) ---
        $upcomingEvents = collect([]);
        if (Module::has('Events') && Module::isEnabled('Events')) {
            $stats['upcoming_events'] = DB::table('events')
                ->where('start_date', '>=', Carbon::now())
                ->count();

            $upcomingEvents = DB::table('events')
                ->where('start_date', '>=', Carbon::now())
                ->orderBy('start_date', 'asc')
                ->take(5)
                ->get();
        }

        if (Module::has('Sermons') && Module::isEnabled('Sermons')) {
            $stats['sermons_count'] = DB::table('sermons')->count();
        }

        if (Module::has('Worship') && Module::isEnabled('Worship')) {
            $stats['worship_songs'] = DB::table('worship_songs')->count();
        }

        if (Module::has('Intercessor') && Module::isEnabled('Intercessor')) {
            $stats['prayer_requests'] = DB::table('prayer_requests')->where('status', 'pending')->count();
        }

        // --- 4. Charts Data ---
        $growthChart = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = DB::table('users')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $growthChart['labels'][] = $date->format('M/Y');
            $growthChart['data'][] = $count;
        }

        // Financial Chart fallback
        $financialChart = ['labels' => [], 'income' => [], 'expense' => []];
        $recentEntries = collect([]);

        return view('admin::dashboard', compact(
            'modulesData',
            'stats',
            'upcomingEvents',
            'recentEntries',
            'growthChart',
            'financialChart'
        ));
    }
}
