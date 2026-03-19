<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('role');

        $stats = [
            'profile_completion' => $user->getProfileCompletionPercentage(),
            'is_baptized' => $user->is_baptized,
            'is_ordained' => $user->is_ordained,
        ];

        return view('memberpanel::dashboard', compact('user', 'stats'));
    }

    // Mantido sem o método calculateProfileCompletion(): o cálculo oficial
    // de completude agora vem sempre de User::getProfileCompletionPercentage().
}
